<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryCrudController extends AbstractController
{
    #[Route('/category/crud', name: 'app_category_crud')]
    public function index(): Response
    {
        return $this->render('category_crud/index.html.twig', [
            'controller_name' => 'CategoryCrudController',
        ]);
    }
}
