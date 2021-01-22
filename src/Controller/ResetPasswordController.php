<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/mdp-oublie", name="reset_password")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        if ($this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email'))
        {
            $user = $manager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user)
            {
                //enregistrer en base le reset password

                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $manager->persist($reset_password);
                $manager->flush();

                //envpyer email à l'utilisateur pour edit son mdp

                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]) ;

                $content = "Bonjour ".$user->getFullName().", vous avez demander de réinitialiser votre mot de passe <br><br>";
                $content .= "Merci de bien vouloir cliquer sur le lien <a href='".$url."'>suivant</a> ";

                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe', $content);

                $this->addFlash('notice-success', 'Vous allez recevoir un email pour réinitialiser votre mot de passe');
            } else {
                $this->addFlash('notice-error', 'Cette email est inconnu');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modifier-mdp/{token}", name="update_password")
     */
    public function update($token, EntityManagerInterface $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $reset_password = $manager->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password)
        {
            return $this->redirectToRoute('reset_password');
        }

        //Verifier si createdAt == now - 3h
        $now = new \DateTime();

        if ($now > $reset_password->getCreatedAt()->modify('+ 3 hour'))
        {
            $this->addFlash('notice', 'Votre demande de mot de passe a expirée. Merci de recommencer.');
            return $this->redirectToRoute('reset_password');
        }

        //Rendre la vue avec mdp
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $new_pwd = $form->get('new_password')->getData();

            $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);

            $reset_password->getUser()->setPassword($password);

            $manager->flush();

            $this->addFlash('notice-success', 'Votre mot de passe a bien été mis à jour');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);

    }

}
