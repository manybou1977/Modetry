<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserCrudController extends AbstractController
{
    #[Route('/user/crud', name: 'app_user_crud')]
    public function index(): Response
    {
        return $this->render('user_crud/index.html.twig', [
            'controller_name' => 'UserCrudController',
        ]);
    }
}
