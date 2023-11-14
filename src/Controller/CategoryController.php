<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $categorys = $paginator->paginate(
            $categoryRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('category/index.html.twig', [
            'categorys' => $categorys,
        ]);
    }

    #[Route('/category/create', name: 'create_category')]
    public function create(
        Request $request, EntityManagerInterface $manager,AuthorizationCheckerInterface $authorizationChecker
    ): Response {

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();
            // dd($form->getData($ingerdient));
            $this->addFlash('success', 'Category created successfully!');
            return $this->redirectToRoute('category');
        }
        return $this->render('category/create.html.twig', [
            //'exams' => $exams,
            'button' => 'Submit',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/edit/{id}', name: 'edit_category')]
    public function edit(CategoryRepository $repository, 
    int $id, Request $request, 
    EntityManagerInterface $manager,AuthorizationCheckerInterface $authorizationChecker): Response
    {

        $category = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $manager->persist($category);
            $manager->flush();
            // dd($form->getData($ingerdient));
            $this->addFlash('success', 'Category created successfully!');
            return $this->redirectToRoute('category');
        }
        return $this->render('category/edit.html.twig', [
            'button' => 'Submit',
            'form' => $form->createView(),

        ]);
    }
    #[Route('/category/delete/{id}', name: 'delete_category', methods: ['GET', 'POST'])]
    public function delete(CategoryRepository $reposetory,
     int $id, Request $request, EntityManagerInterface $manager,
      Category $category,AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')&&!$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        $manager->remove($category);
        $manager->flush();

        $this->addFlash('success', 'Category deleted successfully!');
        return $this->redirectToRoute('category');
    }
}
