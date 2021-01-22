<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{


    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_success")
     */
    public function index($stripeSessionId, EntityManagerInterface $manager, Cart $cart): Response
    {
        $order = $manager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
    if ($order->getState() == 0) {
        //Vider la session cart
        $cart->remove();
        //Modifier le statut isPaid() à 1
        $order->setState(1);
        $manager->flush();
        //Envoyer un email au client pour confirmer la commande
        $mail = new Mail();
        $content = "Bonjour ".$order->getUser()->getFullName().". Merci pour votre commande";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullName(), 'La commande est bien validée', $content);
    }

        //Afficher les quelques informations de la commande de l'utilisateur



        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
