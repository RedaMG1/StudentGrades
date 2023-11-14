<?php

namespace App\Controller;

use App\Repository\GradeRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(UserRepository $userRepository,
    PaginatorInterface $paginator,
    Request $request,GradeRepository $gradeRepository): Response
    {
        $users = $paginator->paginate(
            $userRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        $grades = $paginator->paginate(
            $gradeRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'users' => $users,
            'grades' => $grades,
            
        ]);
    }
}
