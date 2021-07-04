<?php

namespace App\Controller;

use App\Entity\GroupMonitoringSettings;
use App\Form\GroupMonitoringSettingsType;
use App\Repository\GroupMonitoringSettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/monitoring/settings")
 */
class GroupMonitoringSettingsController extends AbstractController
{
    /**
     * @Route("/", name="group_monitoring_settings_index", methods={"GET"})
     */
    public function index(GroupMonitoringSettingsRepository $groupMonitoringSettingsRepository): Response
    {
        return $this->render('group_monitoring_settings/index.html.twig', [
            'group_monitoring_settings' => $groupMonitoringSettingsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="group_monitoring_settings_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $groupMonitoringSetting = new GroupMonitoringSettings();
        $form = $this->createForm(GroupMonitoringSettingsType::class, $groupMonitoringSetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($groupMonitoringSetting);
            $entityManager->flush();

            return $this->redirectToRoute('group_monitoring_settings_index');
        }

        return $this->render('group_monitoring_settings/new.html.twig', [
            'group_monitoring_setting' => $groupMonitoringSetting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_monitoring_settings_show", methods={"GET"})
     */
    public function show(GroupMonitoringSettings $groupMonitoringSetting): Response
    {
        return $this->render('group_monitoring_settings/show.html.twig', [
            'group_monitoring_setting' => $groupMonitoringSetting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="group_monitoring_settings_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GroupMonitoringSettings $groupMonitoringSetting): Response
    {
        $form = $this->createForm(GroupMonitoringSettingsType::class, $groupMonitoringSetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('group_info_show', ['id' => $groupMonitoringSetting->getGroupInfo()->getId()]);
        }

        return $this->render('group_monitoring_settings/edit.html.twig', [
            'group_monitoring_setting' => $groupMonitoringSetting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_monitoring_settings_delete", methods={"POST"})
     */
    public function delete(Request $request, GroupMonitoringSettings $groupMonitoringSetting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupMonitoringSetting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($groupMonitoringSetting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_monitoring_settings_index');
    }
}
