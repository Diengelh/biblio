<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
// use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index')]
    public function index(Request $request,
                          EntityManagerInterface $manager,
                        //   MailerInterface $mailer
                          ): Response
    {
        $contact = new Contact();
        if($this->getUser()) {
            $contact->setName($this->getUser()->getLastname())
                    ->setFirstname($this->getUser()->getFirstname())
                    ->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            #stocker les messages de contact dans la base de donnée (table contact)
            $manager->persist($contact);
            $manager->flush();

            # ou utiliser le service src/MailService pour recevoir les messages (dans MailTrap)
            # Pour cela un dsn a été ajouté dans .env.local
            
            // $email = (new Email())
            // ->from('hello@example.com')
            // ->to('you@example.com')
            // //->cc('cc@example.com')
            // //->bcc('bcc@example.com')
            // //->replyTo('fabien@example.com')
            // //->priority(Email::PRIORITY_HIGH)
            // ->subject('Time for Symfony Mailer!')
            // ->text('Sending emails is fun again!')
            // ->html('<p>See Twig integration for better HTML integration!</p>');

            // $mailer->send($email);

            $this->addFlash(
               'success',
               'Votre message a été bien envoyé avec succès'
            );

            return $this->redirectToRoute('app_main_index');
        }

        return $this->render('main/index.html.twig', [
            'form' => $form,
        ]);
    }
}
