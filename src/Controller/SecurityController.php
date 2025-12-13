<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordModifyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route("/password_modify", name: "app_password", methods:["GET", "POST"])]
    public function password_modify(
        Request $request,
        UserPasswordHasherInterface $hacher,
        EntityManagerInterface $manager
    ) 
    {

        $form = $this->createForm(PasswordModifyType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /** @var User $user */
            $user = ($this->getUser());

            if($hacher->isPasswordValid($user, $form->getData()["old_password"])){

                // Pour que l'eventListener (PreUpdate) se déclenche il faut qu'au moins l'un des champs 
                // de l'entité en question soit modifié (ici nous mettons le mot de passe à vide pour l'instant).
                $user->setPassword("");
                $user->setPlainPassWord($form->getData()["new_password"]);

                //Pour modifier un objet, on a pas besoin d'appeler la fonction persist sur elle.
                $manager->flush();

                $this->addFlash(
                    "success",
                    "Votre mot de passe a été modifié avec succès"
                );

                return $this->redirectToRoute('app_main_index');

            } else {
                $this->addFlash(
                    "danger",
                    "L'ancien mot de passe indiqué est incorrect, Veillez réessayer"
                );
            }

            return $this->redirectToRoute('app_password');
        }

        return $this->render("security/password_modify.html.twig", [
            "form" => $form,
        ]);
    }
}
