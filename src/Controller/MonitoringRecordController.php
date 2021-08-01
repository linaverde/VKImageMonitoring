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

use App\Entity\Publication;
use App\Repository\PublicationRepository;

use App\Entity\Comment;
use App\Repository\CommentRepository;

use App\Entity\Repost;
use App\Repository\RepostRepository;

use VK\Client\VKApiClient;

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
                    'msg' => 'Для сбора данных о репостах необходимо аутентифицироваться ВКонтакте от имени администратора сообщества',
                    'btn1' => 'Перейти в настройки',
                    'btn2' => 'Назад',
                    'id' => $monitoringRecord->getGroupLink()->getId(),
                    'redirect1' => 'group_info_show',
                    'redirect2' => 'monitoring_record_new'
                ]);
            } elseif ($request->get("Reposts")) {
                //проверяем токен админа на актуальность
                $vk = new VKApiClient();
                $group = $monitoringRecord->getGroupLink();
                $url = $group->getLink();
                $groupId = str_replace('https://vk.com/', '', $url);
                try {
                    $response = $vk->groups()->getById($group->getAdminToken(), array(
                        'group_id' => array($groupId),
                        'fields' => array('members_count', 'city', 'country'),
                    ));
                } catch (\Throwable $ex) {
                    return $this->render('warning.html.twig', [
                        'msg' => 'Ваши данные устарели, пожалуйста, пройдите аутентификацию ВКонтакте еще раз и повторите запрос',
                        'btn1' => 'Перейти в настройки',
                        'btn2' => 'Назад',
                        'id' => $monitoringRecord->getGroupLink()->getId(),
                        'redirect1' => 'group_delete_access',
                        'redirect2' => 'monitoring_record_new'
                    ]);
                }
            }


            $entityManager = $this->getDoctrine()->getManager();
            $monitoringRecord->setHasComments(false);
            $monitoringRecord->setHasReposts(false);
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

    /**
     * @Route("/{id}/details", name="monitoring_record_details", methods={"GET"})
     */
    public function details(MonitoringRecord $monitoringRecord): Response
    {

        $params = [];
        $params['monitoring_record'] = $monitoringRecord;

        $subscribers = $monitoringRecord->getRecord();
        $subscribers = $subscribers[0];
        $params['subs'] = $subscribers;

        $posts = $monitoringRecord->getPublications();
        $tops = false;
        $params['posts'] = $posts;

        $group = $monitoringRecord->getGroupLink();
        $url = $group->getLink();
        $params['url'] = $url;

        $vk = new VKApiClient();
        $access_token = $_ENV['VK_SECRET_CODE'];
        $groupId = str_replace('https://vk.com/', '', $url);
        $response = $vk->groups()->getById($access_token, array(
            'group_id' => array($groupId),
            'fields' => array('members_count', 'city', 'country'),
        ));

        $gid = $response[0]['id'];

        $params['groupId'] = $gid;
        if (count($posts) > 3) {





            $tops = true;
            $postRepository = $this->getDoctrine()->getRepository(Publication::class);
            $topViews = $postRepository->findBy(
                ['Record' => $monitoringRecord->getId()],
                ['ViewsCount' => 'DESC']
            );
            $topViews = array_slice($topViews, 0, 3);
            $topLikes = $postRepository->findBy(
                ['Record' => $monitoringRecord->getId()],
                ['LikesCount' => 'DESC']
            );
            $topLikes = array_slice($topLikes, 0, 3);
            $topComments = $postRepository->findBy(
                ['Record' => $monitoringRecord->getId()],
                ['CommentsCount' => 'DESC']
            );
            $topComments = array_slice($topComments, 0, 3);
            $topReposts = $postRepository->findBy(
                ['Record' => $monitoringRecord->getId()],
                ['RepostsCount' => 'DESC']
            );
            $topReposts = array_slice($topReposts, 0, 3);

            $params['tops'] = [$topViews, $topLikes, $topComments, $topReposts];
        }
        $params['showTop'] = $tops;

        $allCount = 0;
        $allPositive = 0;
        $allNegative = 0;
        $allNeutral = 0;

        if ($monitoringRecord->getHasComments()) {
            $params['comments'] = true;
            $publicationRepository = $this->getDoctrine()->getRepository(Publication::class);
            $publications = $publicationRepository->findBy(
                ['Record' => $monitoringRecord->getId()]
            );
            $commentsRepository = $this->getDoctrine()->getRepository(Comment::class);
            $commentsArr = [];
            foreach ($publications as $post) {
                $commentsArr = array_merge($commentsArr, $commentsRepository->findBy(
                    ['Publication' => $post->getId()]
                ));
            }
            $positiveComments = 0;
            $negativeComments = 0;
            $neutralComments = 0;
            foreach ($commentsArr as $comment) {
                if ($comment->getMood() == '+') {
                    $positiveComments += 1;
                } elseif ($comment->getMood() == '-') {
                    $negativeComments += 1;
                } else {
                    $neutralComments += 1;
                }
            }
            $params['commentsCount'] = count($commentsArr);
            $params['positiveComments'] = $positiveComments;
            $params['negativeComments'] = $negativeComments;
            $params['neutralComments'] = $neutralComments;

            $allCount += count($commentsArr);
            $allPositive += $positiveComments;
            $allNegative += $negativeComments;
            $allNeutral += $neutralComments;

            if (count($commentsArr) >= 3) {
                usort($commentsArr, function ($comment1, $comment2) {
                    if ($comment1->getLikesCount() == $comment2->getLikesCount()) return 0;
                    return ($comment1->getLikesCount() > $comment2->getLikesCount()) ? -1 : 1;
                });
                $commentsTop = array_slice($commentsArr, 0, 3);
                $params['commentsTop'] = $commentsTop;
            } else {
                $params['commentsTop'] = null;
            }
        } else {
            $params['comments'] = false;
        }

        if ($monitoringRecord->getHasReposts()) {
            $params['reposts'] = true;
            $publicationRepository = $this->getDoctrine()->getRepository(Publication::class);
            $publications = $publicationRepository->findBy(
                ['Record' => $monitoringRecord->getId()]
            );
            $repostRepository = $this->getDoctrine()->getRepository(Repost::class);
            $repostArr = [];
            foreach ($publications as $post) {
                $repostArr = array_merge($repostArr, $repostRepository->findBy(
                    ['Publication' => $post->getId()]
                ));
            }
            $positiveReposts = 0;
            $negativeReposts = 0;
            $neutralReposts = 0;
            foreach ($repostArr as $repost) {
                if ($repost->getMood() == '+') {
                    $positiveReposts += 1;
                } elseif ($repost->getMood() == '-') {
                    $negativeReposts += 1;
                } else {
                    $neutralReposts += 1;
                }
            }
            $params['repostsCount'] = count($repostArr);
            $params['positiveReposts'] = $positiveReposts;
            $params['negativeReposts'] = $negativeReposts;
            $params['neutralReposts'] = $neutralReposts;

            $allCount += count($repostArr);
            $allPositive += $positiveReposts;
            $allNegative += $negativeReposts;
            $allNeutral += $neutralReposts;

            if (count($repostArr) >= 3) {
                usort($repostArr, function ($repost1, $repost2) {
                    if ($repost1->getLikesCount() == $repost2->getLikesCount()) return 0;
                    return ($repost1->getLikesCount() > $repost2->getLikesCount()) ? -1 : 1;
                });
                $repostTop = array_slice($repostArr, 0, 3);
                $params['repostsTop'] = $repostTop;
            } else {
                $params['repostsTop'] = null;
            }

        } else {
            $params['reposts'] = false;
        }

        if ($allCount > 0) {
            $params['image'] = (($allPositive + $allNeutral - $allNegative)/$allCount)*10;
        } else {
            $params['image'] = null;
        }


        return $this->render('monitoring_record/details.html.twig', $params);
    }


}
