<?php
namespace App\EventListener;

use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Pour les évenements de doctrine il faut ajouter un tag
 * dans le fichier services.yaml
 * Et pour les autres événements il suffit juste d'établir l'évenement avec
 * un attribut comme on la fait ici "onAfterSuccessConnexion"
 */

class UserListener
{
    private UserPasswordHasherInterface $hacher;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserPasswordHasherInterface $hacher,
        EntityManagerInterface $entityManager
    )
    {
        $this->hacher = $hacher;
        $this->entityManager = $entityManager;
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if($event->getObject() instanceof User){
            $this->encode($event->getObject());
        }
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        if($event->getObject() instanceof User){
            /** @var User $user */
            $user = $event->getObject();
            if ($user->getPlainPassword()) {
                $this->encode($user);
            }
        }
    }

    // procédure qui encode le mot de passe en clair d'un user donné
    // en paramètre.
    private function encode(User $user) 
    {

        $encodedPassword = $this->hacher->hashPassword(
            $user,
            $user->getPlainPassWord()
        );
        $user->setPassword($encodedPassword);
        // effacer le mot de passe en clair de l'utilisateur pour plus de 
        // de sécurité.
        $user->setPlainPassWord(null);
    }

    #[AsEventListener(event:SecurityEvents::INTERACTIVE_LOGIN)]
    public function onAfterSuccessConnexion(InteractiveLoginEvent $event){

        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $user->setConnectedAt(new DateTimeImmutable());
        $this->entityManager->flush();

    }
    
}