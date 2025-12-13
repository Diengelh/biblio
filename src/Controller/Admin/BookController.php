<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_admin_book_index')]
    public function index(BookRepository $repository,
                          Request $request,
                          PaginatorInterface $paginator): Response
    {
        $query = $repository->createQueryBuilder("a");
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('admin/book/index.html.twig', [
            "pagination" => $pagination,
        ]);
    }

    #[IsGranted("ROLE_AJOUT_DE_LIVRE")]
    #[Route("/{id}/edit", name: "app_admin_book_edit", requirements: ["id" => "\d+"], methods: ["GET", "POST"])]
    #[Route('/new', name: 'app_admin_book_new', methods: ["GET", "POST"])]
    public function new(?Book $book, Request $request, EntityManagerInterface $manager): Response
    {
        // s'il s'agit d'une édition de livre
        if($book != null){ //équivalent à if($book){....}
            //refuser l'accès si l'utilisateur connecté n'est pas administrateur
            // ni le créateur de ce livre.
            
            $this->denyAccessUnlessGranted("book.is_creator", $book); //fait appel au voter BookCreatorVoter
        }

        $book ??= new Book();
        $form = $this->createForm(BookType::class, $book);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // s'il s'agit pas d'une édition alors on doit faire le lien entre le livre et son créateur
            
            // récupérer l'utilisateur connecté
            $user = $this->getUser();
            if($book->getId() == null && $user instanceof User){
                $user->addBook($book);
            }

            //envoyer les données de l'objet book dans la base de donnée
            $manager->persist($book);
            $manager->flush();

            //faire la redirection vers le formulaire
            return $this->redirectToRoute("app_admin_book_index");
        }

        return $this->render('admin/book/new.html.twig', [
            'form' => $form,
            "book_id" => $book != null ? $book->getId(): null 
        ]);
    }

    #[Route("/{id}", name: "app_admin_book_show", requirements: ["id" => "\d+"], methods: ["GET"])]
    public function show(?Book $book) : Response
    {
        return $this->render("admin/book/show.html.twig", [
            "book" => $book,
        ]);
    }

}
