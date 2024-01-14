<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogPostCrudController extends AbstractController
{
    #[Route('/blog/post/crud', name: 'app_blog_post_crud')]
    public function index(): Response
    {
        return $this->render('blog_post_crud/index.html.twig', [
            'controller_name' => 'BlogPostCrudController',
        ]);
    }
}
