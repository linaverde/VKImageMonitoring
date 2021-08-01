<?php

// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use VK\Client\VKApiClient;
use Vk\Exceptions\VKApiException;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\MonitoringRecord;
use App\Repository\MonitoringRecordRepository;

use App\Entity\GroupInfo;
use App\Repository\GroupInfoRepository;

use App\Entity\Publication;
use App\Repository\PublicationRepository;

use App\Entity\Comment;
use App\Repository\CommentRepository;

use App\Entity\Repost;
use App\Repository\RepostRepository;

use App\Entity\GroupMonitoringSettings;
use App\Repository\GroupMonitoringSettingsRepository;

use App\Entity\User;
use App\Repository\UserRepository;

use App\Entity\UserMonitoringSettings;
use App\Repository\UserMonitoringSettingsRepository;

use App\Entity\SubsctiberInfo;
use App\Repository\SubsctiberInfoRepository;

class VKController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/processing/{id}/{params}", name="monitoring_start")
     */
    public function getRecordsInfo(Request $request, $id, $params)
    {
        set_time_limit(0);

        $path = $_ENV['PYTHON_PATH'];
        $dbname = $_ENV['DB_NAME'];
        $dbuser = $_ENV['DB_USER'];
        $dbpass = $_ENV['DB_PASS'];

        $record = $this->em->getRepository(MonitoringRecord::class)->find($id);
        $json = json_decode($params, true);

        $vk = new VKApiClient();
        $access_token = $_ENV['VK_SECRET_CODE'];

        $group = $record->getGroupLink();

        //токен администратора для получения репостов
        $adminToken = $group->getAdminToken();

        $url = $group->getLink();
        $groupId = str_replace('https://vk.com/', '', $url);

        $response = $vk->groups()->getById($access_token, array(
            'group_id' => array($groupId),
            'fields' => array('members_count', 'city', 'country'),
        ));

        $gid = $response[0]['id'];

        if ($json['s']) {
            //достаем из информации о группе поля для сравнения
            $city = $group->getCity();
            $country = $group->getCountry();
            $minAge = $group->getMinAge();
            $maxAge = $group->getMaxAge();
            $gender = $group->getGender();

            //создаем значения для заполнения

            $maleCount = 0;
            $femaleCount = 0;
            $ageCount = 0;
            $smallerAgeCount = 0;
            $biggerAgeCount = 0;
            $cityCount = 0;
            $countryCount = 0;

            //запрос количества пользователей
            $response = $vk->groups()->getMembers($access_token, array(
                'group_id' => $gid,
                'offset' => 0,
                'count' => 0
            ));

            $user_count = $response['count'];
            $curr_offset = 0;
            do {
                //запрос списка пользователей
                $response = $vk->groups()->getMembers($access_token, array(
                    'group_id' => $gid,
                    'offset' => $curr_offset,
                    'count' => 1000
                ));
                $users = $response['items'];

                usleep(500000);//задержка между запросами

                $response = $vk->users()->get($access_token, array(
                    'user_ids' => $users,
                    'fields' => array('sex', 'bdate', 'city', 'country',),
                    'lang' => 'ru'
                ));
                foreach ($response as $item) {
                    //проверяем пол
                    if ($item['sex'] == 1) {
                        $femaleCount++;
                    } elseif ($item['sex'] == 2) {
                        $maleCount++;
                    }

                    if (array_key_exists('bdate', $item)) {
                        $bday = explode('.', $item['bdate']);
                        if (count($bday) == 3) {
                            $age = $this->getAge($bday[0], $bday[1], $bday[2]);
                            if ($minAge != null && $maxAge != null && $minAge <= $age && $age <= $maxAge ||
                                $minAge != null && $minAge <= $age ||
                                $maxAge != null && $age <= $maxAge
                            ) {
                                $ageCount++;
                            } elseif ($minAge != null && $minAge > $age) {
                                $smallerAgeCount++;
                            } elseif ($maxAge != null && $maxAge < $age) {
                                $biggerAgeCount++;
                            }
                        }
                    }

                    if (array_key_exists('city', $item)) {
                        if ($city != null && $item['city']['title'] == $city)
                            $cityCount++;
                    }

                    if (array_key_exists('country', $item)) {
                        if ($country != null && $item['country']['title'] == $country)
                            $countryCount++;
                    }

                }
                $curr_offset += 1000;
            } while ($curr_offset < $user_count);
            //создаем новый объект мониторинга и сохраняем в него собранную информацию
            $subscriber_info = new SubsctiberInfo();
            $subscriber_info->setRecord($record);
            $subscriber_info->setCount($user_count);
            $subscriber_info->setMaleCount($maleCount);
            $subscriber_info->setFemaleCount($femaleCount);
            $subscriber_info->setExpectedAgeCount($ageCount);
            $subscriber_info->setSmallerAgeCount($smallerAgeCount);
            $subscriber_info->setBiggerAgeCount($biggerAgeCount);
            $subscriber_info->setExpectedCityCount($cityCount);
            $subscriber_info->setExpectedCountryCount($countryCount);
            $subscriber_info->setMinAge($minAge);
            $subscriber_info->setMaxAge($maxAge);
            $subscriber_info->setCountry($country);
            $subscriber_info->setCity($city);
            $subscriber_info->setGender($gender);

            $this->em->persist($subscriber_info);
            //$this->em->flush();

        }

        if ($json['p']) {
            //получаем настройки для конкретного сообщества
            $daysCount = 0;
            $postsCount = 0;
            $settings = $this->em->getRepository(GroupMonitoringSettings::class)->findOneBy([
                'GroupInfo' => $group,

            ]);
            $userSettings = $this->em->getRepository(UserMonitoringSettings::class)->findOneBy([
                'User' => $group->getUser(),
            ]);
            if ($settings) {
                $daysCount = $settings->getDaysCount();
                $postsCount = $settings->getPostsCount();
                echo 'group posts count = ' . $postsCount;
                if (!$daysCount) {
                    $daysCount = $userSettings->getDaysCount();
                }
                if (!$postsCount) {
                    $postsCount = $userSettings->getPostsCount();
                }
            } else {
                $daysCount = $userSettings->getDaysCount();
                $postsCount = $userSettings->getPostsCount();
                echo 'user posts count = ' . $postsCount;
            }

            //получаем количество постов
            $curr_offset = 0;
            $response = $vk->wall()->get($access_token, array(
                'owner_id' => "-" . $gid,
                'offset' => $curr_offset,
                'count' => 0
            ));

            echo 'all posts = ' . $response['count'] . ' ';

            if (!$postsCount) {
                $postsCount = $response['count'];
            }

            echo 'posts count = ' . $postsCount;

            if ($postsCount < 100) {
                $responcePostsCount = $postsCount;
            } else {
                $responcePostsCount = 100;
            }

            do {

                usleep(500000);//задержка между запросами

                $response = $vk->wall()->get($access_token, array(
                    'owner_id' => "-" . $gid,
                    'offset' => $curr_offset,
                    'count' => $responcePostsCount
                ));

                $posts = $response['items'];

                foreach ($posts as $item) {

                    if ($daysCount && $this->getDays($item['date']) > $daysCount) {
                        break;
                    }

                    $post = new Publication();
                    $post->setRecord($record);
                    $post->setLink($item['id']);
                    if (array_key_exists('views', $item)) {
                        $post->setViewsCount($item['views']['count']);
                    } else {
                        $post->setViewsCount(-1);
                    }
                    $post->setLikesCount($item['likes']['count']);
                    $post->setCommentsCount($item['comments']['count']);
                    $post->setRepostsCount($item['reposts']['count']);
                    $this->em->persist($post);
                    //$this->em->flush();

                    if ($json['c']) {

                        $currComment = 0;

                        $commentResponce = $vk->wall()->getComments($access_token, array(
                            'owner_id' => "-" . $gid,
                            'post_id' => $item['id'],
                            'offset' => $currComment,
                            'count' => 0
                        ));

                        $commentsCount = $commentResponce['count'];

                        if ($commentsCount > 0) {

                            do {
                                usleep(1000000);//задержка между запросами

                                $commentResponce = $vk->wall()->getComments($access_token, array(
                                    'owner_id' => "-" . $gid,
                                    'post_id' => $item['id'],
                                    'offset' => $currComment,
                                    'count' => 1,
                                    'need_likes' => 1,
                                    'extended' => 1

                                ));

                                echo ' postid' . $item['id'];

                                echo json_encode($commentResponce);

                                if (count($commentResponce['items']) > 0) {

                                    $comment = new Comment();
                                    $comment->setPublication($post);
                                    $comment->setText($commentResponce['items'][0]['text']);
                                    $comment->setLikesCount($commentResponce['items'][0]['likes']['count']);
                                    if (count($commentResponce['groups']) == 1) {
                                        $comment->setUserType('g');
                                    } else {
                                        $comment->setUserType('u');
                                        $authorResponse = $vk->users()->get($access_token, array(
                                            'user_id' => $commentResponce['profiles'][0]['id'],
                                            'fields' => array('sex', 'bdate'),
                                            'lang' => 'ru'
                                        ));

                                        if ($authorResponse[0]['sex'] == 1) {
                                            $comment->setUserGender('f');
                                        } elseif ($authorResponse[0]['sex'] == 2) {
                                            $comment->setUserGender('m');
                                        }

                                        if (array_key_exists('bdate', $authorResponse[0])) {
                                            $bday = explode('.', $authorResponse[0]['bdate']);
                                            if (count($bday) == 3) {
                                                $age = $this->getAge($bday[0], $bday[1], $bday[2]);
                                                $comment->setUserAge($age);
                                            }
                                        }
                                    }
                                    // определение тональности
                                    $text = $commentResponce['items'][0]['text'];
                                    if ($text != ""){
                                        $comment->setMood($this->getMood($commentResponce['items'][0]['text']));
                                    } else {
                                        $comment->setMood("=");
                                    }

                                    $this->em->persist($comment);
                                    //$this->em->flush();
                                }

                                $currComment += 1;

                            } while ($currComment < $commentsCount);
                        }
                    }

                    if ($json['r']) {

                        $currRepost = 0;

                        $repostsCount = $post->getRepostsCount();
                        echo "reposts count " . $repostsCount . ' ';

                        //проверка на актуальность токена
                        try {
                            $repostResponce = $vk->wall()->getReposts($adminToken, array(
                                'owner_id' => "-" . $gid,
                                'post_id' => $item['id'],
                                'offset' => $currRepost,
                                'count' => 0
                            ));

                        } catch (\Throwable $ex) {

                        }

                        if ($repostsCount > 0) {

                            do {

                                usleep(500000);//задержка между запросами

                                $repostResponce = $vk->wall()->getReposts($adminToken, array(
                                    'owner_id' => "-" . $gid,
                                    'post_id' => $item['id'],
                                    'offset' => $currRepost,
                                    'count' => 1
                                ));

                                $repost = new Repost();
                                $repost->setPublication($post);
                                $repost->setLink($repostResponce['items'][0]['id']);
                                $repost->setText($repostResponce['items'][0]['text']);
                                $repost->setLikesCount($repostResponce['items'][0]['likes']['count']);
                                if (count($repostResponce['profiles']) == 1) {
                                    $repost->setUserType('u');
                                    $authorResponse = $vk->users()->get($access_token, array(
                                        'user_id' => $repostResponce['profiles'][0]['id'],
                                        'fields' => array('sex', 'bdate'),
                                        'lang' => 'ru'
                                    ));

                                    if ($authorResponse[0]['sex'] == 1) {
                                        $repost->setUserGender('f');
                                    } elseif ($authorResponse[0]['sex'] == 2) {
                                        $repost->setUserGender('m');
                                    }

                                    if (array_key_exists('bdate', $authorResponse[0])) {
                                        $bday = explode('.', $authorResponse[0]['bdate']);
                                        if (count($bday) == 3) {
                                            $age = $this->getAge($bday[0], $bday[1], $bday[2]);
                                            $repost->setUserAge($age);
                                        }
                                    }
                                } else {
                                    $repost->setUserType('g');

                                }

                                if ($repost->getText()) {
                                    $repost->setMood($this->getMood($repost->getText()));
                                } else {
                                    $repost->setMood('=');
                                }

                                $this->em->persist($repost);
                                //$this->em->flush();

                                $currRepost += 1;
                            } while ($currRepost < $repostsCount);
                        }

                    }

                }

                $curr_offset += 100;
            } while ($curr_offset < $postsCount);

            if ($json['c']) {
                $record->setHasComments(true);
            } else {
                $record->setHasComments(false);
            }

            if ($json['r']) {
                $record->setHasReposts(true);
            } else {
                $record->setHasReposts(false);
            }
        }

        $record->setStatus(1);
        $this->em->flush();

        sleep(20);

        if ($json['s']){
            $output = shell_exec("python3 " . $path . 'graph/subscribers.py ' . $subscriber_info->getId() . ' -db=' . $dbname . ' -u=' . $dbuser . ' -p=' . "'" . $dbpass . "' 2>&1");
            echo $output;
        }

        if ($json['c']) {
            $output = shell_exec("python3 " . $path . 'graph/commentMood.py ' . $record->getId() . ' -db=' . $dbname . ' -u=' . $dbuser . ' -p=' . "'" . $dbpass . "' 2>&1");
            echo $output;

        }

        if ($json['r']) {
            $output = shell_exec("python3 " . $path . 'graph/repostMood.py ' . $record->getId() . ' -db=' . $dbname . ' -u=' . $dbuser . ' -p=' . "'" . $dbpass . "' 2>&1");
            echo $output;
        }

        return new Response();

    }

    function getAge($d, $m, $y)
    {
        if ($m > date('m') || $m == date('m') && $d > date('d'))
            return (date('Y') - $y - 1);
        else
            return (date('Y') - $y);
    }

    function getDays($date)
    {
        $now = time();
        $datediff = $now - $date;

        return floor($datediff / (60 * 60 * 24));

    }

    function getMood($message)
    {
        $path = $_ENV['PYTHON_PATH'] . "mood/main.py";

        $output = shell_exec("python3 " . $path . " " . "'" . $message . "'");

        $outputjson = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $output),
            true);

        if ($outputjson) {
            $positive = 0;
            $neutral = 0;
            $negative = 0;
            $skip = 0;
            if (array_key_exists('positive', $outputjson)) {
                $positive = $outputjson['positive'];
            }
            if (array_key_exists('negative', $outputjson)) {
                $negative = $outputjson['negative'];
            }
            if (array_key_exists('neutral', $outputjson)) {
                $neutral = $outputjson['neutral'];
            }
            if (array_key_exists('skip', $outputjson)) {
                $skip = $outputjson['skip'];
            }

            if ($positive == max([$positive, $neutral, $negative, $skip])) {
                return '+';
            } elseif ($negative == max([$positive, $neutral, $negative, $skip])) {
                return '-';
            } else {
                return '=';
            }
        } else {
            return '=';
        }
    }

}