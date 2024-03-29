<?php

namespace App\Controller;

use App\Entity\Produits;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
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
    private function calculTotal(array $panier): float
    {
        $total=0;
        foreach($panier as $produit){
            $total += $produit['quantite']* $produit['prix'];
        }
        return $total;
    }
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $panier= $session->get('panier',[]);
        $total=$this->calculTotal($panier);
        $session->set('total',$total);
    
        return $this->render('panier/index.html.twig', [
            'total' =>$total,
            'panier' =>$panier,
        ]);
    }
    #[Route('/panier/ajout/{produitId}',name: 'app_ajout_panier')]
    public function ajoutPanier($produitId, SessionInterface $session,Request $request)
    {


        $panier=$session->get('panier',[]);
        $produitPanier=$this->manager->getRepository(Produits::class)->find($produitId);

       
       $tailleSelectionnee = $request->request->get('taille');

        if(empty($tailleSelectionnee)){
            $this->addFlash('error','Veuillez sélectionner une taille');
            return $this->redirectToRoute('app_affichage');
        }

        if (isset($panier[$produitId])){
            $panier[$produitId]['quantite']++;

        } else{
            $produit=$this->manager->getRepository(Produits::class)->find($produitId);
            if($produit && $tailleSelectionnee ){
                $tailles=$produit->getTailles();
                 //foreach($tailles as $taille){

                $panier[$produitId]=[
                    'id'=>$produit->getId(),
                    'quantite'=>1,
                    'nom'=>$produit->getNom(),
                    'prix'=>$produit->getPrix(),
                    'taille'=>$tailleSelectionnee,
                    'couleur'=>$produit->getCouleur(),
                    'image'=>$produit->getImage(),
                ];
               //  }
            }
        }
        $session->set('panier',$panier);
        $total=$this->calculTotal($panier);
        $session->set('total',$total);
        return $this->redirectToRoute('app_panier',[
            'total'=>$total
        ]);
    }
    #[Route('/panier/suppression/{produitId}',name:'app_suppression_panier')]
    public function suppressionPanier($produitId, sessionInterface $session)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $panier=$session->get('panier',[]);
        if (isset($panier[$produitId])){
            unset($panier[$produitId]);
        }
        $session->set('panier', $panier);
        $total=$this->calculTotal($panier);
        $session->set('total',$total);
        return $this->redirectToRoute('app_panier',[
            'total'=>$total
        ]);
    }
    #[Route('/suppression',name:'app_suppression_session')]
    public function suprSession(sessionInterface $session)
    {
        $session->invalidate();
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/augmenter/{produitId}', name: 'app_augmenter_panier')]
public function augmenterPanier($produitId,SessionInterface $session)
{
    $panier = $session->get('panier',[]);
    if(isset($panier[$produitId])){
        $panier[$produitId]['quantite']++;
    }
    $session->set('panier',$panier);
    $total=$this->calculTotal($panier);
    $session->set('total_panier', $total);
    return $this->redirectToRoute('app_panier',[
        'total'=>$total
    ]);
}

#[Route('/panier/diminuer/{produitId}', name: 'app_diminuer_panier')]
public function diminuerPanier($produitId,SessionInterface $session)
{
    $panier = $session->get('panier',[]);
    if(isset($panier[$produitId]) && $panier[$produitId]['quantite']>1){
        $panier[$produitId]['quantite']--;
    }else {
        unset($panier[$produitId]);
    }
    $session->set('panier',$panier);
    $total=$this->calculTotal($panier);
    $session->set('total_panier', $total);
    return $this->redirectToRoute('app_panier',[
        'total'=>$total
    ]);
}
}
