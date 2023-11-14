<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GradeRepository;
use App\Entity\Grade;
use App\Entity\User;
use App\Form\GradeType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GradeController extends AbstractController
{
    #[Route('/grade', name: 'app_grade')]
    public function index(
        GradeRepository $gradeRepository,
        PaginatorInterface $paginator,
        Request $request,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $grades = $paginator->paginate(
            $gradeRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        $category = $categoryRepository->find(1);
        // foreach ($grades as $grade) {
        //     $user = $userRepository->find($grade->getUser());
        //     $grade->setUser($user);}
        
        
        // $grade = $gradeRepository->find(2)->setCategory($category);
        return $this->render('grade/index.html.twig', [
            'grades' => $grades,
        ]);
    }
    #[Route('/grade/create', name: 'create_grade', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $manager,
        CategoryRepository $categoryRepository,
        GradeRepository $gradeRepository,AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $grade = new Grade();
        $form = $this->createForm(GradeType::class, $grade);
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            
            if ($grade->getGrade() > 10) {
                $category = $categoryRepository->find(1);
                $category->addGrade($grade);
             
            } else {
                $category = $categoryRepository->find(2);
                $category->addGrade($grade);
            }
            $manager->persist($grade);
            $manager->flush();
            // dd($grade->getUser());
            $this->addFlash('success', 'Grade created successfully!');
            return $this->redirectToRoute('grade');
        }
        return $this->render('grade/create.html.twig', [
            //'exams' => $exams,
            'button' => 'Submit',
            'form' => $form->createView(),
            'selectedUser' => $grade->getUser(),
        ]);
    }
    #[Route('/grade/edit/{id}', name: 'edit_grade', methods: ['GET', 'POST'])]
    public function edit(GradeRepository $reposetory, int $id, 
    Request $request, EntityManagerInterface $manager, CategoryRepository $categoryRepository,
    AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $grade = $reposetory->findOneBy(['id' => $id]);
        $form = $this->createForm(GradeType::class, $grade);
        $form->handleRequest($request);
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if ($grade->getGrade() > 10) {
                $category = $categoryRepository->find(1);
                $category->addGrade($grade);
             
            } else {
                $category = $categoryRepository->find(2);
                $category->addGrade($grade);
            }
            $grade = $form->getData();
            $manager->persist($grade);
            $manager->flush();
            // dd($form->getData($ingerdient));
            $this->addFlash('success', 'Grade edited successfully!');
            return $this->redirectToRoute('grade');
        }
        return $this->render('grade/edit.html.twig', [
            'button' => 'Submit',
            'form' => $form->createView(),

        ]);
    }
    #[Route('/grade/delete/{id}', name: 'delete_grade', methods: ['GET', 'POST'])]
    public function delete(GradeRepository $reposetory, 
    int $id, Request $request, EntityManagerInterface $manager,
     Grade $grade,AuthorizationCheckerInterface $authorizationChecker): Response
    {if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
        throw new AccessDeniedException();
        return $this->render('access_denied.html.twig');
    }

        $manager->remove($grade);
        $manager->flush();

        $this->addFlash('success', 'Grade deleted successfully!');
        return $this->redirectToRoute('grade');
    }
}
