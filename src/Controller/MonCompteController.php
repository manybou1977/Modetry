<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\TryOn;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ModificationMonCompteFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MonCompteController extends AbstractController
{
    private $manager;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();   
    }
    #[Route('/mon-compte', name: 'app_mon_compte')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $userId = $this->getUser();
        $photo = $this->manager->getRepository(TryOn::class)->findBy([
            'user'=>$userId->getId()
        ]);
        //dump($photo);
        //die;
        return $this->render('mon_compte/index.html.twig', [
            'tryon' => $photo,
        ]);
    }

    #[Route('/mon-compte/modification/{id}', name: 'app_modification_mon_compte')]
    public function modificationMonCompte(Request $request,User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form=$this->createForm(ModificationMonCompteFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
         
            $this->manager->persist($user);
            $this->manager->flush();
            return $this->redirectToRoute('app_mon_compte', []);
        }
        return $this->render('mon_compte/modification.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
