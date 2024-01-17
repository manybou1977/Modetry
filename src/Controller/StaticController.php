<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    #[Route('/qui/sommes/nous', name: 'app_qui_sommes_nous')]
    public function index(): Response
    {
        return $this->render('static/index.html.twig', [
            'controller_name' => 'QuiSommesNousController',
        ]);
    }
    #[Route('/cgu', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('static/cgu.html.twig', [
            
        ]);
    }
    #[Route('/cgv', name: 'app_cgv')]
    public function cgv(): Response
    {
        return $this->render('static/cgv.html.twig', [
            
        ]);
    }
    #[Route('/politique_de_confidentialite', name: 'app_politique_de_confidentialite')]
    public function politique_de_confidentialite(): Response
    {
        return $this->render('static/politique_de_confidentialite.html.twig', [
            
        ]);
    }
}
