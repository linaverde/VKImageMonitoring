<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\UploadedLogFile;
use App\Form\LogType;
use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/log")
 */
class LogController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @Route("/", name="log_index", methods={"GET"})
     */
    public function index(LogRepository $logRepository): Response
    {
        return $this->render('log/index.html.twig', [
            'logs' => $logRepository->findAll(),
        ]);
    }

    /**
     * @Route("/upload", name="upload")
     */
    public function upload_log(): Response
    {
        return $this->render('log/upload.html.twig');
    }

    /**
     * @Route("/upload_script", name="upload_script")
     */
    public function upload_script(Request $request): Response
    {
//        $path = $_ENV['PYTHON_PATH'] . "log/main.py";
//        $file = $_ENV['FILES_PATH'] . 'access.log';
//        exec('python3 ' . $path . " " . $file . ' -o=uri', $output, $code);

        $uploaddir = $_ENV['FILES_PATH'] . 'logs/';
        $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            //Check file hash after uploaded
            $hash = hash_file("sha256", $uploadfile);

            $repository = $this->getDoctrine()->getRepository(UploadedLogFile::class);
            $existed = $repository->findOneBy([
                'hash' => $hash,
            ]);
            if (!$existed) {

                $dbname = $_ENV['DB_NAME'];
                $dbuser = $_ENV['DB_USER'];
                $dbpass = $_ENV['DB_PASS'];

                $path = $_ENV['PYTHON_PATH'] . "log/main.py";

                exec('python3 ' . $path . ' ' . $uploadfile . ' -db=' . $dbname . ' -t=log -u=' . $dbuser . ' -p=' . "'" . $dbpass . "'",
                    $output, $code);
                if ($code == 0) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $logFile = new UploadedLogFile();
                    $logFile->setHash($hash);
                    $logFile->setCreatedBy($this->security->getUser());
                    $entityManager->persist($logFile);
                    $entityManager->flush();
                    return $this->render('log/success.html.twig');
                } else {
                    return new Response("Ошибка записи в базу данных");
                }
            } else {
                return new Response("Вы уже загружали файл с идентичным содержимым ранее");
            }
        } else {
            return new Response("Возможная атака с помощью файловой загрузки!");
        }

    }

    /**
     * @Route("/new", name="log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $log = new Log();
        $form = $this->createForm(LogType::class, $log);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($log);
            $entityManager->flush();

            return $this->redirectToRoute('log_index');
        }

        return $this->render('log/new.html.twig', [
            'log' => $log,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="log_show", methods={"GET"})
     */
    public function show(Log $log): Response
    {
        return $this->render('log/show.html.twig', [
            'log' => $log,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Log $log): Response
    {
        $form = $this->createForm(LogType::class, $log);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('log_index');
        }

        return $this->render('log/edit.html.twig', [
            'log' => $log,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="log_delete", methods={"POST"})
     */
    public function delete(Request $request, Log $log): Response
    {
        if ($this->isCsrfTokenValid('delete' . $log->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($log);
            $entityManager->flush();
        }

        return $this->redirectToRoute('log_index');
    }
}
