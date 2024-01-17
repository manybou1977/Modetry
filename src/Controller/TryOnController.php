<?php

namespace App\Controller;

use App\Entity\TryOn;
use App\Form\TryOnType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TryOnController extends AbstractController
{   private $manager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    #[Route('/try/on/{id}', name: 'app_try_on')]
    public function index(Request $request, SluggerInterface $slugger, $id): Response
    {
        $user = $this->getUser();
        $tryOn = new TryOn;
        $tryOn->setUser($user);
        $tryOn->setLiker("liker");
        $form = $this->createForm(TryOnType::class, $tryOn);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPhotoFile = $form->get('photo')->getData();

            if ($newPhotoFile) {
                $cheminOrigine = pathinfo($newPhotoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($cheminOrigine);
                $nouveauChemin = $safeFilename . '-' . uniqid() . '.' . $newPhotoFile->guessExtension();

                try {
                    $newPhotoFile->move(
                        $this->getParameter('IMG_URL_USER'),
                        $nouveauChemin
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
                    return $this->redirectToRoute('app_avis_produits', ['id' => $id]);
                }
                $tryOn->setPhoto($nouveauChemin);
            }
            $this->manager->persist($tryOn);
            $this->manager->flush();
            return $this->redirectToRoute('app_mon_compte', ['id' => $id]);
        }
			return $this->render('try_on/index.html.twig',[
                'form'=>$form->createView(),
                
            ]);
    }

    #[Route('/try_on/modification/{id}', name: 'app_modification_try_on')]
    public function modificationTryOn(Request $request,TryOn $tryOn, SluggerInterface $slugger ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form=$this->createForm(TryOnType::class, $tryOn);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
         
            $newPhotoFile = $form->get('photo')->getData();

            if ($newPhotoFile) {
                $cheminOrigine = pathinfo($newPhotoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($cheminOrigine);
                $nouveauChemin = $safeFilename . '-' . uniqid() . '.' . $newPhotoFile->guessExtension();

                try {
                    $newPhotoFile->move(
                        $this->getParameter('IMG_URL_USER'),
                        $nouveauChemin
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
                    return $this->redirectToRoute('app_avis_produits', ['id' => $id]);
                }
                $tryOn->setPhoto($nouveauChemin);
            }

            $this->manager->persist($tryOn);
            $this->manager->flush();
            return $this->redirectToRoute('app_mon_compte', []);
        }
        return $this->render('try_on/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/try_on/supprime/{id}', name: 'app_supprime_try_on')]
    public function suppressionTryOn(int $id, Request $request): Response
    {
        if ($this->isCsrfTokenValid('suppression',$request->query->get('token',''))){
            if($id){
                $tryOn = $this->manager->getRepository(TryOn::class)->find($id);
                $this->manager->remove($tryOn);
                $this->manager->flush();
            }
            return $this->redirectToRoute('app_mon_compte');
        }else {
            throw new BadRequestException('token csrf invalid');
        }
    }
}
