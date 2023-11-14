<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/user', name: 'app_user')]
    public function index(
        UserRepository $userRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $users = $paginator->paginate(
            $userRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/create', name: 'create_user', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $manager,AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $currentDate = date('Y-m-d H:i:s');
        $form->handleRequest($request);
        if (!$authorizationChecker->isGranted('ROLE_ADMIN') && !$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt($currentDate);
            $user->setUpdatedAt($currentDate);

            $manager->persist($user);
            $manager->flush();
            // dd($form->getData($ingerdient));
            $this->addFlash('success', 'User created successfully!');
            return $this->redirectToRoute('user');
        }
        return $this->render('user/create.html.twig', [
            //'exams' => $exams,
            'button' => 'Submit',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/user/edit/{id}', name: 'edit_user', methods: ['GET', 'POST'])]
    public function edit(
        UserRepository $reposetory,
        AuthorizationCheckerInterface $authorizationChecker,
        int $id,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $user = $reposetory->findOneBy(['id' => $id]);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $currentDate = date('Y-m-d H:i:s');

        if (!$authorizationChecker->isGranted('ROLE_ADMIN') && !$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt($currentDate);
            $user->setUpdatedAt($currentDate);

            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();
            // dd($form->getData($user));
            $this->addFlash('success', 'User edited successfully!');
            return $this->redirectToRoute('user');
        }
        return $this->render('user/edit.html.twig', [

            'button' => 'Submit',
            'form' => $form->createView(),

        ]);
    }
    #[Route('/user/delete/{id}', name: 'delete_user', methods: ['GET', 'POST'])]
    public function delete(
        UserRepository $reposetory,
        int $id,
        Request $request,
        EntityManagerInterface $manager,
        User $user,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN') && !$authorizationChecker->isGranted('ROLE_TEACHER')) {
            throw new AccessDeniedException();
            
            return $this->render('access_denied.html.twig');
        }
        $manager->remove($user);
        $manager->flush();
        $this->addFlash('success', 'User deleted successfully!');
        return $this->redirectToRoute('user');
    }
}
