<?php


namespace App\Controller;


use Stripe;

use App\Service\AppHelpers;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Security;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    private $app;
    private $db;
    private $cartCount;
    private $session;
    

    public function __construct(Security $security, ManagerRegistry $doctrine, RequestStack $requestStack) {
        $this->db = $doctrine->getManager();
        $this->session = $requestStack->getSession();
        // $this->totalPanier = $sessionPanier->get('total');

        // on simule le montant obtenu depuis la page de commande:

        $panier= $requestStack->getSession()->get('panier',[]);
        $this->session->set('orderTotal', $this->calculTotal($panier));
    }

    private function calculTotal(array $panier): float
    {
        $total=0;
        foreach($panier as $produit){
            $total += $produit['quantite']* $produit['prix'];
        }
        return $total;
    }

       #[Route('/stripe',name: 'app_stripe')] 
    public function index(Session $session): Response {
        $total = $session->get('total');

        return $this->render('stripe/index.html.twig', [
            'clef_stripe' => $_ENV["STRIPE_KEY"],
            'cartCount' => $this->cartCount,
            'orderTotal' => $this->session->get('orderTotal'),
            'total' => $total,
        ]);
    }

        #[Route('/stripe/create-charge',name: 'app_stripe_charge')]
    public function createCharge(Request $request){
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        try {
            Stripe\Charge::create([
                "amount" => $this->session->get('orderTotal') * 100,
                "currency" => "eur",
                "source" => $request->request->get('stripeToken'),
                "description" => "Payment Test"
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_stripe_fail', [
                'error' => $e->getMessage(),
            ], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_stripe_success', [], Response::HTTP_SEE_OTHER);
    }

        #[Route('/order/confirmation',name: 'app_stripe_success')]
    public function orderConfirmation(Session $session): Response {

        $total = $session->get('total');

        $session->invalidate();

        return $this->render('stripe/order_confirmation.html.twig', [
            'cartCount' => $this->cartCount,
            'total' => $total,
        ]);

    }


        #[Route('/payment/failure',name: 'app_stripe_fail')]
    public function paymentFailure(Request $request, Session $session): Response {

        $total = $session->get('total');
        $error = $request->get('error');

        return $this->render('stripe/payment_failure.html.twig', [
            'cartCount' => $this->cartCount,
            'error' => $error,
            'total' => $total,
        ]);
    }
}