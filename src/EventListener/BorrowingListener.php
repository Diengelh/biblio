<?php
namespace App\EventListener;

use App\Entity\Book;
use App\Enum\BookStatus;
use App\Entity\Borrowing;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BorrowingListener
{
    private EntityManagerInterface $manager;

    public function __construct(
        EntityManagerInterface $manager
    )
    {
        $this->manager = $manager;
    }

    /**
     * Mettre à jour le champ status du livre emprunté selon
     * qu'il est disponible ou pas après chaque emprunt.
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        if($event->getObject() instanceof Borrowing){
            /** @var Borrowing $borrowing */
            $borrowing = $event->getObject();
            $this->setStatusBook($borrowing->getBook());
            $this->manager->flush($borrowing->getBook());
        }
    }

    /**
     * mettre à jour la disponiblité du livre quand il est rendu.
     */
    public function preRemove(LifecycleEventArgs $event)
    {
        if($event->getObject() instanceof Borrowing){
            /** @var Borrowing $borrowing */
            $borrowing = $event->getObject();
            $borrowing->getBook()->setStatus(BookStatus::Available);
            $this->manager->flush($borrowing->getBook());
        }
    }

    /**
     * mettre à jour le champ status du livre donné en paramètre.
     */
    private function SetStatusBook(Book $book){
        if(count($book->getBorrowings()) == $book->getStock()){
            $book->setStatus(BookStatus::Unavailable);
        } else {
            $book->setStatus(BookStatus::Available);
        }
    }
}