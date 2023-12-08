<?php

namespace App\Controller;

use App\Entity\Tailles;
use App\Form\TaillesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaillesController extends AbstractController
{
    private $manager;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();   
    }
    #[Route('/tailles', name: 'app_tailles')]
    public function index(Request $request): Response
    {
        $tailles = new Tailles;
        $form = $this->createForm(TaillesType::class, $tailles,[
            'method'=>'GET'
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
           $this->manager->persist($tailles);
           $this->manager->flush(); 
           return $this->redirectToRoute('app_tailles');
        }
        return $this->render('tailles/index.html.twig', [
            'form' =>$form->createView() ,
        ]);
    }
}
