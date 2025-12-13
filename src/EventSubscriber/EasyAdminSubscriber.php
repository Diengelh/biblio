<?php

namespace App\EventSubscriber;

use App\Entity\Book;
use App\Entity\User;
use DateTimeImmutable;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\Form\Event\PreSetDataEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private UserRepository $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $repository
    )
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => ['onAfterSuccessConnexion'], // on peux le supprimer car on l'a fait dans UserListener.
            PostSetDataEvent::class => ['postSetData', 3000],
            BeforeEntityPersistedEvent::class => ['onBeforeEntityPersisted'],
        ];    
    }

    /**
     * Cette fonction s'éxécute à chaque fois qu'un utilisateur soit 
     * connecté, et change la date de dernière connection.
     */
    public function onAfterSuccessConnexion(InteractiveLoginEvent $event){

        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $user->setConnectedAt(new DateTimeImmutable());
        $this->entityManager->flush();

    }

    public function postSetData(PreSetDataEvent $event){
        dd($event);
    }

    public function onBeforeEntityPersisted(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if ($entity instanceof Book) {
            // Manipulez les données avant de les sauvegarder
            // Exemple : $entity->setSomeField('new value');
        }
    }
}


