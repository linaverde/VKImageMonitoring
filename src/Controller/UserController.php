<?php

// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\UserMonitoringSettingsRepository;
use App\Entity\UserMonitoringSettings;

class UserController extends AbstractController
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
     * @Route("/", name="start_page")
     */
    public function start_page(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->render('startpage.html.twig');
        } else {
            return $this->redirectToRoute('user_profile');
        }
    }


    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile(): Response
    {
        return $this->render('users/profile.html.twig');
    }


    /**
     * @Route("/profile/settings", name="user_settings")
     */
    public function settings(UserMonitoringSettingsRepository $userMonitoringSettingsRepository): Response
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

        return $this->render('users/settings.html.twig', [
            'user' => $user,

        ]);
    }

    /**
     * @Route("/profile/settings/change_name", name="change_name")
     */
    public function change_name(Request $request, UserRepository $repository): Response
    {
        $user = $this->security->getUser();
        $user->setOptionalName($request->request->get('Link'));
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_settings');
    }



}