<?php

namespace App\Controller;

use App\Entity\Produits;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    private $manager;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();   
    }
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session): Response
    {
        $panier= $session->get('panier',[]);
        return $this->render('panier/index.html.twig', [
            'panier' =>$panier,
        ]);
    }
    #[Route('/panier/ajout/{produitId}',name: 'app_ajout_panier')]
    public function ajoutPanier($produitId, SessionInterface $session)
    {
        $panier=$session->get('panier',[]);
        if (isset($panier[$produitId])){
            $panier[$produitId]['quantite']++;

        } else{
            $produit=$this->manager->getRepository(Produits::class)->find($produitId);
            if($produit){
                $tailles=$produit->getTailles();
                foreach($tailles as $taille){

                $panier[$produitId]=[
                    'quantite'=>1,
                    'nom'=>$produit->getNom(),
                    'prix'=>$produit->getPrix(),
                    'taille'=>$taille->getMesures(),
                    'couleur'=>$produit->getCouleur(),
                    'image'=>$produit->getImage(),
                ];
                 }
            }
        }
        $session->set('panier',$panier);
        return $this->redirectToRoute('app_panier');
    }
    #[Route('/panier/supression/{produitId}',name:'app_suppression_panier')]
    public function suppressionPanier($produitId, sessionInterface $session)
    {
        $panier=$session->get('panier',[]);
        if (isset($panier[$produitId])){
            unset($panier[$produitId]);
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('app_panier');
    }
}
