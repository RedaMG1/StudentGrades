<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ExamRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Exam;
use App\Form\ExamType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ExamController extends AbstractController
{
    #[Route('/exam', name: 'app_exam')]
    public function index(
        ExamRepository $examRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $exams = $paginator->paginate(
            $examRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('exam/index.html.twig', [
            'exams' => $exams,
        ]);
    }

    #[Route('/exam/create', name: 'create_exam', methods: ['GET', 'POST'])]
    public function create(Request $request, 
    EntityManagerInterface $manager,AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $exam = new Exam();
        $form = $this->createForm(ExamType::class, $exam);

        $form->handleRequest($request);
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($exam);
            $manager->flush();
            // dd($form->getData($ingerdient));
            $this->addFlash('success', 'Exam created successfully!');
            return $this->redirectToRoute('exam');
        }
        return $this->render('exam/create.html.twig', [
            //'exams' => $exams,
            'button' => 'Submit',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/exam/edit/{id}', name: 'edit_exam', methods: ['GET', 'POST'])]
    public function edit(
        ExamRepository $reposetory,
        AuthorizationCheckerInterface $authorizationChecker,
        int $id,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $exam = $reposetory->findOneBy(['id' => $id]);
        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);

        if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {

            $exam = $form->getData();
            $manager->persist($exam);
            $manager->flush();
            // dd($form->getData($exam));
            $this->addFlash('success', 'Exam edited successfully!');
            return $this->redirectToRoute('exam');
        }
        return $this->render('exam/edit.html.twig', [

            'button' => 'Submit',
            'form' => $form->createView(),

        ]);
    }
    #[Route('/exam/delete/{id}', name: 'delete_exam', methods: ['GET', 'POST'])]
    public function delete(ExamRepository $reposetory, int $id, Request $request,
     EntityManagerInterface $manager, Exam $exam,
     AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        $manager->remove($exam);
        $manager->flush();

        $this->addFlash('success', 'Exam deleted successfully!');
        return $this->redirectToRoute('exam');
    }
}
