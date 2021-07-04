<?php

namespace App\Controller;

use App\Entity\GroupInfo;
use App\Form\GroupInfo1Type;
use App\Repository\GroupInfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use VK\OAuth\VKOAuth;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuthResponseType;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponceType;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\GroupMonitoringSettings;
use App\Repository\GroupMonitoringSettingsRepository;


/**
 * @Route("/group/info")
 */
class GroupInfoController extends AbstractController
{

    /**
     * @Route("/", name="group_info_index", methods={"GET"})
     */
    public function index(GroupInfoRepository $groupInfoRepository): Response
    {
        return $this->render('group_info/index.html.twig', [
            'group_infos' => $groupInfoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="group_info_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $groupInfo = new GroupInfo();
        $form = $this->createForm(GroupInfo1Type::class, $groupInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $url = $task->getLink();
            $user = $task->getUser();

            if (strpos($url, 'https://vk.com/') !== false) {
                $groupId = str_replace('https://vk.com/', '', $url);
                $vk = new VKApiClient();
                $access_token = $_ENV['VK_SECRET_CODE'];
                try {
                    $response = $vk->groups()->getById($access_token, array(
                        'group_id' => $groupId,
                        'fields' => array('members_count', 'city', 'country'),
                    ));
                    $entityManager = $this->getDoctrine()->getManager();
                    $savedGroups = $this->getDoctrine()
                        ->getRepository(GroupInfo::class)
                        ->findBy([
                            'User' => $user,
                            'Link' => $url
                        ]);
                    if (!$savedGroups) {
                        $entityManager->persist($groupInfo);
                        $entityManager->flush();
                        return $this->redirectToRoute('user_settings');
                    } else {
                        return $this->render('message.html.twig', [
                            'msg' => 'Вы уже добавляли эту группу',
                            'redirect' => 'group_info_new'
                        ]);
                    }
                } catch (VKApiException $ex) {
                    $error_code = $ex->getCode();
                    if ($error_code == 100) {
                        return $this->render('message.html.twig', [
                            'msg' => 'Ошибка в ссылке на группу',
                            'redirect' => 'group_info_new'
                        ]);
                    } elseif ($error_code == 203){
                        return $this->render('message.html.twig', [
                            'msg' => 'Вы пытаетесь добавить закрытую группу',
                            'redirect' => 'group_info_new'
                        ]);
                    }
                }

            }

            return $this->redirectToRoute('user_settings');

        }

        return $this->render('group_info/new.html.twig', [
            'group_info' => $groupInfo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_info_show", methods={"GET"})
     */
    public function show(GroupInfo $groupInfo, GroupMonitoringSettingsRepository $groupMonitoringSettingsRepository): Response
    {

        $settings = $groupMonitoringSettingsRepository->findBy([
            'GroupInfo' => $groupInfo,
        ]);
        if (!$settings) {
            $entityManager = $this->getDoctrine()->getManager();
            $currGroupSettings = new GroupMonitoringSettings();
            $currGroupSettings->setGroupInfo($groupInfo);
            $entityManager->persist($currGroupSettings);
            $entityManager->flush();
        }

        return $this->render('group_info/show.html.twig', [
            'group_info' => $groupInfo,
        ]);
    }

    /**
     * @Route("/{id}/delete_access", name="group_delete_access", methods={"GET"})
     */
    public function deleteAccess(GroupInfoRepository $groupInfoRepository, $id): Response
    {

        $groupInfo = $groupInfoRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $groupInfo->setAdminToken(null);
        $groupInfo->setAdminName(null);
        $entityManager->flush();

        return $this->render('group_info/show.html.twig', [
            'group_info' => $groupInfo,
        ]);
    }

    /**
     * @Route("/{id}/access", name="group_gain_access", methods={"GET"})
     */
    public function gainAccess($id): Response
    {

        $oauth = new VKOAuth();
        $client_id = $_ENV['VK_ID'];
        $redirect_uri = 'http://206.189.222.242/group/info/' . $id . '/token/';
        $display = VKOAuthDisplay::PAGE;
        $scope = array(VKOAuthUserScope::WALL, VKOAuthUserScope::GROUPS);
        $state = 'secret_state_code';

        $browser_url = $oauth->getAuthorizeUrl(VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope, $state);

        return $this->redirect($browser_url);
    }

    /**
     * @Route("/{id}/token", name="group_gain_token", methods={"GET"})
     */
    public function gainToken(Request $request, GroupInfoRepository $groupInfoRepository, $id): Response
    {
        $request->query->all();

        $oauth = new VKOAuth();
        $client_id =  $_ENV['VK_ID'];
        $client_secret = $_ENV['VK_SECURE_CODE'];
        $redirect_uri = 'http://206.189.222.242/group/info/' . $id . '/token/';
        $code = $request->query->get('code');

        $response = $oauth->getAccessToken($client_id, $client_secret, $redirect_uri, $code);
        $access_token = $response['access_token'];
        $userId = $response['user_id'];

        $vk = new VKApiClient();
        try {
            $response = $vk->users()->get($access_token, array(
                'user_ids' => array($userId),
            ));
            $json = $response[0];
            $groupInfo = $groupInfoRepository->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $groupInfo->setAdminToken($access_token);
            $groupInfo->setAdminName($json['first_name']. ' ' . $json['last_name']);
            $entityManager->flush();

            return $this->redirectToRoute('group_info_show', [
                'id' => $id,
            ]);
        }
        catch (VKApiException $ex) {
            return new Response($ex->getCode() . $ex->getMessage());
        }


    }

    /**
     * @Route("/{id}/edit", name="group_info_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GroupInfo $groupInfo): Response
    {
        $form = $this->createForm(GroupInfo1Type::class, $groupInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->render('group_info/show.html.twig', [
                'group_info' => $groupInfo,
            ]);
        }

        return $this->render('group_info/edit.html.twig', [
            'group_info' => $groupInfo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_info_delete", methods={"POST"})
     */
    public function delete(Request $request, GroupInfo $groupInfo): Response
    {
        if ($this->isCsrfTokenValid('delete' . $groupInfo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($groupInfo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_info_index');
    }
}
