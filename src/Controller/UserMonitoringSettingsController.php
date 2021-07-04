<?php

namespace App\Controller;

use App\Entity\UserMonitoringSettings;
use App\Form\UserMonitoringSettingsType;
use App\Repository\UserMonitoringSettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/defaultmonitoings")
 */
class UserMonitoringSettingsController extends AbstractController
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * @Route("/", name="user_monitoring_settings_index", methods={"GET"})
     */
    public function index(UserMonitoringSettingsRepository $userMonitoringSettingsRepository): Response
    {
        $user = $this->security->getUser();
        $settings = $userMonitoringSettingsRepository->findBy([
            'User' =>  $user,
        ]);
        if (!$settings){
            $entityManager = $this->getDoctrine()->getManager();
            $currUserSettings = new UserMonitoringSettings();
            $currUserSettings->setUser($user);
            $entityManager->persist($currUserSettings);
            $entityManager->flush();

        }

        return $this->render('user_monitoring_settings/index.html.twig', [
            'user_monitoring_settings' => $userMonitoringSettingsRepository->findBy([
                'User' =>  $user,
            ])
        ]);
    }

    /**
     * @Route("/new", name="user_monitoring_settings_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $userMonitoringSetting = new UserMonitoringSettings();
        $form = $this->createForm(UserMonitoringSettingsType::class, $userMonitoringSetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userMonitoringSetting);
            $entityManager->flush();

            return $this->redirectToRoute('user_monitoring_settings_index');
        }

        return $this->render('user_monitoring_settings/new.html.twig', [
            'user_monitoring_setting' => $userMonitoringSetting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_monitoring_settings_show", methods={"GET"})
     */
    public function show(UserMonitoringSettings $userMonitoringSetting): Response
    {
        return $this->render('user_monitoring_settings/show.html.twig', [
            'user_monitoring_setting' => $userMonitoringSetting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_monitoring_settings_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserMonitoringSettings $userMonitoringSetting): Response
    {
        $form = $this->createForm(UserMonitoringSettingsType::class, $userMonitoringSetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_settings');
        }

        return $this->render('user_monitoring_settings/edit.html.twig', [
            'user_monitoring_setting' => $userMonitoringSetting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_monitoring_settings_delete", methods={"POST"})
     */
    public function delete(Request $request, UserMonitoringSettings $userMonitoringSetting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userMonitoringSetting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userMonitoringSetting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_monitoring_settings_index');
    }
}
