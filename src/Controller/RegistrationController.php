<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ["GET", "POST"])]
    public function register(
            Request $request,
            EntityManagerInterface $entityManager
                           ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // initialiser le mot de passe en clair pour qu'il soit accessible
            // lors de l'appel en UserListener pour encoder le mot de passe.
            $user->setPlainPassWord($form->get('plainPassword')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            // faire ce que tu veux ici, comme envoyer un email (tu peux 
            // utiliser une Entity Listener pour le faire)

            return $this->redirectToRoute('app_main_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
