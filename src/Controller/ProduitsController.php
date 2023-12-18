<?php

namespace App\Controller;

use DateTime;
use App\Entity\Avis;
use App\Entity\Produits;
use App\Entity\Categorie;
use App\Form\ProduitsType;
use App\Form\FormulaireAvisType;
use Doctrine\ORM\EntityManagerInterface;
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
            return $this->redirectToRoute('app_affichage', []);
        }
        return $this->render('produits/modification.html.twig', [
            "form"=> $form->createView()


        ]);
    } 
    #[Route('/produits/supprime/{id}', name: 'app_produits_supprime')]
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
    #[Route('/produits/avis/{id}', name:'app_avis_produits')]
    public function produitsAvis(Request $request,$id){
        $produit=$this->manager->getRepository(Produits::class)->find($id);
        $user=$this->getUser();
        $avis=new Avis();
        $avis->setUser($user);
        $avis->setProduits($produit);
        $avis->setDatePublication(new \DateTime('today')  );
        $form=$this->createForm(FormulaireAvisType::class,$avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->manager->persist($avis);
            $this->manager->flush();

            return $this->redirectToRoute('app_avis_produits',['id'=>$id]);

        }
        $commentaire=$produit->getAvis();
        // usort($commantaire, function($a,$b){
        //     return $b->getDatePublication
        // })
        
        return $this->render('produits/detailsProduits.html.twig',[
            'produit'=>$produit,
            'commentaire'=>$commentaire,
            'form'=>$form->createView(),

        ]);
        
    }
    #[Route('/produit/femme',name:'app_produit_femme')]
    public function affichageFemme(EntityManagerInterface $entityManager): Response
{
    $repositoryProduit = $entityManager->getRepository(Produits::class);
    $repositoryCategorie = $entityManager->getRepository(Categorie::class);

   
    $genreFemme = 'Femme';
    $nomCategorie = 'Vêtements';

    $query = $repositoryProduit->createQueryBuilder('p')
        ->join('p.categorie', 'c')
        ->where('c.genre = :genre')
        ->setParameter('genre', $genreFemme)
        ->getQuery();

    $produitFemme = $query->getResult();

    return $this->render('produits/femme.html.twig', [
        "produitFemme" => $produitFemme,
        "nomCategorie" => $nomCategorie,
    ]);
}

#[Route('/produit/femme/chaussures',name:'app_produit_femme_chaussures')]
public function affichageFemmeChaussures(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genreFemme = 'Femme';
$nomCategorie = 'Chaussures';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genreFemme,'nom'=>$nomCategorie])
    ->getQuery();

$produitFemme = $query->getResult();

return $this->render('produits/femme.html.twig', [
    "produitFemme" => $produitFemme,
    "nomCategorie" => $nomCategorie,
]);
}

#[Route('/produit/femme/accessoires',name:'app_produit_femme_accessoires')]
public function affichageFemmeAccessoires(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genreFemme = 'Femme';
$nomCategorie = 'Accessoires';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genreFemme,'nom'=>$nomCategorie])
    ->getQuery();

$produitFemme = $query->getResult();

return $this->render('produits/femme.html.twig', [
    "produitFemme" => $produitFemme,
    "nomCategorie" => $nomCategorie,
]);
}

#[Route('/produit/femme/robe',name:'app_produit_femme_robe')]
public function affichageFemmeRobe(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genreFemme = 'Femme';
$nomCategorie = 'Robes';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genreFemme,'nom'=>$nomCategorie])
    ->getQuery();

$produitFemme = $query->getResult();

return $this->render('produits/femme.html.twig', [
    "produitFemme" => $produitFemme,
    "nomCategorie" => $nomCategorie,
]);
}

#[Route('/produit/femme/jupe',name:'app_produit_femme_jupe')]
public function affichageFemmeJupe(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genreFemme = 'Femme';
$nomCategorie = 'Jupes';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genreFemme,'nom'=>$nomCategorie])
    ->getQuery();

$produitFemme = $query->getResult();

return $this->render('produits/femme.html.twig', [
    "produitFemme" => $produitFemme,
    "nomCategorie" => $nomCategorie,
]);
}

#[Route('/produit/femme/pantalon',name:'app_produit_femme_pantalon')]
public function affichageFemmePantalon(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genreFemme = 'Femme';
$nomCategorie = 'Pantalons';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genreFemme,'nom'=>$nomCategorie])
    ->getQuery();

$produitFemme = $query->getResult();

return $this->render('produits/femme.html.twig', [
    "produitFemme" => $produitFemme,
    "nomCategorie" => $nomCategorie,
]);
}

#[Route('/produit/femme/marque',name:'app_produit_femme_marque')]
public function affichageFemmeMarque(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genreFemme = 'Femme';
$nomCategorie = 'Marques';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genreFemme,'nom'=>$nomCategorie])
    ->getQuery();

$produitFemme = $query->getResult();

return $this->render('produits/femme.html.twig', [
    "produitFemme" => $produitFemme,
    "nomCategorie" => $nomCategorie,
]);
}

#[Route('/produit/homme', name : 'app_produit_homme')]
public function affichageHomme(EntityManagerInterface $entityManager): Response
{
    $repositoryProduit = $entityManager->getRepository(Produits::class);
    $repositoryCategorie = $entityManager->getRepository(Categorie::class);

   
    $genreHomme = 'Homme';
    $nomCategorie = 'Vêtements';

    $query = $repositoryProduit->createQueryBuilder('p')
        ->join('p.categorie', 'c')
        ->where('c.genre = :genre')
        ->setParameter('genre', $genreHomme)
        ->getQuery();

    $produitHomme = $query->getResult();

    return $this->render('produits/homme.html.twig', [
        "produitHomme" => $produitHomme,
        "nomCategorie" => $nomCategorie,
    ]);
}

#[Route('/produit/homme/pantalon',name:'app_produit_homme_pantalon')]
public function affichageHommePantalon(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genre = 'Homme';
$nomCategorie = 'Pantalons';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genre,'nom'=>$nomCategorie])
    ->getQuery();

$produitHomme = $query->getResult();

return $this->render('produits/homme.html.twig', [
    "produitHomme" => $produitHomme,
    "nomCategorie" => $nomCategorie,
]);
}
#[Route('/produit/homme/veste',name:'app_produit_homme_veste')]
public function affichageHommeVeste(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genre = 'Homme';
$nomCategorie = 'Vestes';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genre,'nom'=>$nomCategorie])
    ->getQuery();

$produitHomme = $query->getResult();

return $this->render('produits/homme.html.twig', [
    "produitHomme" => $produitHomme,
    "nomCategorie" => $nomCategorie,
]);
}

#[Route('/produit/homme/chemise',name:'app_produit_homme_chemise')]
public function affichageHommeChemise(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genre = 'Homme';
$nomCategorie = 'Chemises';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genre,'nom'=>$nomCategorie])
    ->getQuery();

$produitHomme = $query->getResult();

return $this->render('produits/homme.html.twig', [
    "produitHomme" => $produitHomme,
    "nomCategorie" => $nomCategorie,
]);
}
#[Route('/produit/homme/accessoire',name:'app_produit_homme_accessoire')]
public function affichageHommeAccessoire(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genre = 'Homme';
$nomCategorie = 'Accessoires';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genre,'nom'=>$nomCategorie])
    ->getQuery();

$produitHomme = $query->getResult();

return $this->render('produits/homme.html.twig', [
    "produitHomme" => $produitHomme,
    "nomCategorie" => $nomCategorie,
]);
}
#[Route('/produit/homme/chaussure',name:'app_produit_homme_chaussure')]
public function affichageHommeChaussure(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genre = 'Homme';
$nomCategorie = 'Chaussures';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genre,'nom'=>$nomCategorie])
    ->getQuery();

$produitHomme = $query->getResult();

return $this->render('produits/homme.html.twig', [
    "produitHomme" => $produitHomme,
    "nomCategorie" => $nomCategorie,
]);
}
#[Route('/produit/homme/marque',name:'app_produit_homme_marque')]
public function affichageHommeMarque(EntityManagerInterface $entityManager): Response
{
$repositoryProduit = $entityManager->getRepository(Produits::class);
$repositoryCategorie = $entityManager->getRepository(Categorie::class);


$genre = 'Homme';
$nomCategorie = 'Marques';

$query = $repositoryProduit->createQueryBuilder('p')
    ->join('p.categorie', 'c')
    ->where('c.genre = :genre')
    ->andWhere('c.nom = :nom')
    ->setParameters(['genre'=> $genre,'nom'=>$nomCategorie])
    ->getQuery();

$produitHomme = $query->getResult();

return $this->render('produits/homme.html.twig', [
    "produitHomme" => $produitHomme,
    "nomCategorie" => $nomCategorie,
]);
}
}
