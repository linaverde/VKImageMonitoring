<?php

namespace App\Controller;

use App\Entity\MonitoringRecord;
use App\Form\MonitoringRecordType;
use App\Repository\MonitoringRecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/profile/monitoring")
 */
class MonitoringRecordController extends AbstractController
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
     * @Route("/", name="monitoring_record_index", methods={"GET"})
     */
    public function index(MonitoringRecordRepository $monitoringRecordRepository): Response
    {
        return $this->render('monitoring_record/index.html.twig', [
            'monitoring_records' => $monitoringRecordRepository->findBy(
                ['User' => $this->security->getUser()->getId()],
                ['id' => 'DESC']
            ),
        ]);
    }

    /**
     * @Route("/new", name="monitoring_record_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $monitoringRecord = new MonitoringRecord();
        $form = $this->createForm(MonitoringRecordType::class, $monitoringRecord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$request->get("Reposts") && !$request->get("Subscribers") && !$request->get('Posts') && !$request->get('Comments')) {
                return $this->render('message.html.twig', [
                    'msg' => 'Необходимо выделить хотя бы один пункт для проведения мониторинга!',
                    'redirect' => 'monitoring_record_new'
                ]);
            } elseif ($request->get("Reposts") && !$monitoringRecord->getGroupLink()->getAdminToken()) {
                return $this->render('warning.html.twig', [
                    'msg' => 'Для сбора данных о репостах необходимо аутентифицироваться ВКонтакте от имени администартора сообщества',
                    'btn1' => 'Перейти в настройки',
                    'btn2' => 'Назад',
                    'id' => $monitoringRecord->getGroupLink()->getId(),
                    'redirect1' => 'group_info_show',
                    'redirect2' => 'monitoring_record_new'
                ]);
            } else {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($monitoringRecord);
                $entityManager->flush();

                $s = ($request->get("Subscribers")) ? 'true' : 'false';
                $p = ($request->get("Posts")) ? 'true' : 'false';
                $c = ($request->get("Comments")) ? 'true' : 'false';
                $r = ($request->get("Reposts")) ? 'true' : 'false';

                return $this->render('monitoring_record/processing.html.twig', [
                    'id' => $monitoringRecord->getId(),
                    's' => $s,
                    'p' => $p,
                    'c' => $c,
                    'r' => $r
                ]);
            }
        }

        return $this->render('monitoring_record/new.html.twig', [
            'monitoring_record' => $monitoringRecord,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="monitoring_record_show", methods={"GET"})
     */
    public function show(MonitoringRecord $monitoringRecord): Response
    {
        return $this->render('monitoring_record/show.html.twig', [
            'monitoring_record' => $monitoringRecord,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="monitoring_record_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MonitoringRecord $monitoringRecord): Response
    {
        $form = $this->createForm(MonitoringRecordType::class, $monitoringRecord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('monitoring_record_index');
        }

        return $this->render('monitoring_record/edit.html.twig', [
            'monitoring_record' => $monitoringRecord,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="monitoring_record_delete", methods={"POST"})
     */
    public function delete(Request $request, MonitoringRecord $monitoringRecord): Response
    {
        if ($this->isCsrfTokenValid('delete' . $monitoringRecord->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($monitoringRecord);
            $entityManager->flush();
        }

        return $this->redirectToRoute('monitoring_record_index');
    }
}
