<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Mark;
use App\Form\MarkType;
use App\Repository\BookRepository;
use App\Repository\MarkRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BookController extends AbstractController
{
    /* 
     * affiches tous les livres de la base de données.
    */
    #[Route('/book', name: 'app_book')]
    public function index(BookRepository $repository,
                          PaginatorInterface $paginator,
                          Request $request): Response
    {
        $query = $repository->createQueryBuilder("b");
        $books = $paginator->paginate(
                    $query,
                    $request->query->getInt("page", 1),
                    4
        );

        return $this->render('book/index.html.twig', [
            "books" => $books,
        ]);
    }

    /*
    * affiche le livre d'identifiant id.
    */
    #[Route("book/{id}", name: "app_book_show", 
                              requirements: ["id" => "\d+"],
                              methods: ["GET", "POST"])
     ]
    public function show(?Book $book,
                         Request $request,
                         MarkRepository $repository,
                         EntityManagerInterface $manager
                        ): Response
    {
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $existingMark = $repository->findBy(
                ["user" => $this->getUSer(), "book" => $book]
            );
            if($existingMark) {
                $this->addFlash(
                    "danger",
                    "Vous avez déjà noté ce livre"
                );
            } else {

                $mark->setUser($this->getUser())
                     ->setBook($book);
                $manager->persist($mark);
                $manager->flush();

                $this->addFlash(
                    "success",
                    "Votre avis a été bien pris en compte"
                );
            }
            
            return $this->redirectToRoute("app_book_show", ["id" => $book->getId()]);
        }

        return $this->render("book/show.html.twig", [
            "book" => $book,
            "form" => $form,
        ]);
    }

}
