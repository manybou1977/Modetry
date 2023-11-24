<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ProduitsController extends AbstractController
{
    private $manager;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();   
    }
    #[Route('/produits', name: 'app_produits')]
    public function index(Request $request,SluggerInterface $slugger): Response
    {
        $produit = new Produits;
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPhotoFile = $form->get('image')->getData();

            if ($newPhotoFile) {
                $cheminOrigine = pathinfo($newPhotoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($cheminOrigine);
                $nouveauChemin = $safeFilename . '-' . uniqid() . '.' . $newPhotoFile->guessExtension();

                try {
                    $newPhotoFile->move(
                        $this->getParameter('IMG_URL_PRODUIT'),
                        $nouveauChemin
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
                    return $this->redirectToRoute('app_produits');
                }
                $produit->setImage($nouveauChemin);
            }
            $this->manager->persist($produit);
            $this->manager->flush();
            return $this->redirectToRoute('app_produits', []);
        }
        return $this->render('produits/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/produit/affichage', name: 'app_affichage')]
    public function affichage(): Response
    {


        $produits = $this->manager->getRepository(Produits::class)->findAll();
        
        return $this->render('produits/affichage.html.twig', [
            "produits"=> $produits


        ]);
    } 

    #[Route('/produit/modification/{id}', name: 'app_modification')]
    public function modificationProduit(Produits $produit,Request $request,SluggerInterface $slugger): Response
    {


        $form=$this->createForm(ProduitsType::class,$produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPhotoFile = $form->get('image')->getData();

            if ($newPhotoFile) {
                $cheminOrigine = pathinfo($newPhotoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($cheminOrigine);
                $nouveauChemin = $safeFilename . '-' . uniqid() . '.' . $newPhotoFile->guessExtension();

                try {
                    $newPhotoFile->move(
                        $this->getParameter('IMG_URL_PRODUIT'),
                        $nouveauChemin
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
                    return $this->redirectToRoute('app_produits');
                }
                $produit->setImage($nouveauChemin);
            }
            $this->manager->persist($produit);
            $this->manager->flush();
            return $this->redirectToRoute('app_produits', []);
        }
        return $this->render('produits/modification.html.twig', [
            "form"=> $form->createView()


        ]);
    } 
    #[route('/produits/supprime/{id}', name: 'app_produits_supprime')]
    public function suppressionProduits(int $id, Request $request): Response
    {
        if ($this->isCsrfTokenValid('suppression',$request->query->get('token',''))){
            if($id){
                $produit = $this->manager->getRepository(Produits::class)->find($id);
                $this->manager->remove($produit);
                $this->manager->flush();
            }
            return $this->redirectToRoute('app_produits');
        }else {
            throw new BadRequestException('token csrf invalid');
        }
    }

}
