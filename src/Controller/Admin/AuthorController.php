<?php

namespace App\Controller\Admin;

use App\Form\AuthorType;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//namespace pour utiliser EntityManager
use Doctrine\ORM\EntityManagerInterface;
//namespace pour la pagination
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/author')]
class AuthorController extends AbstractController
{
    #[Route('', name: 'app_admin_author_index')]
    public function index(Request $request,
                          AuthorRepository $repository,
                          PaginatorInterface $paginator
                          ): Response
    {
        $dates = [];
        //vérifier si la date de départ est défini dans l'url
        if($request->query->has("start")){
            $dates["start"] = $request->query->get("start");
        }
        //même chose pour la date de fin
        if($request->query->has("end")){
            $dates["end"] = $request->query->get("end");
        }

        $query = $repository->findByDateOfBirth($dates);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        return $this->render('admin/author/index.html.twig', [
            'controller_name' => 'AuthorController',
            'pagination' => $pagination,
        ]);
    }
    
    #[IsGranted("ROLE_AJOUT_DE_LIVRE")]
    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: "app_admin_author_edit", requirements: ["id" => "\d+"], methods: ["GET", "POST"])]
    public function new(?Author $author, Request $request, EntityManagerInterface $manager): Response
    {
        if($author){
            $this->denyAccessUnlessGranted("ROLE_EDITION_DE_LIVRE");
        } 

        $author ??= new Author(); // si author est à null une nouvelle instance est affecté à la variable author sinon rien ne passe
        
        //création d'un formulaire
        $form = $this->createForm(AuthorType::class, $author);

        //traiter le formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // stocker dans la base de données l'objet author qui été rempli dans le formulaire
            $manager->persist($author);
            $manager->flush();

            // faire une redirection vers la page d'affichage (show) de l'auteur enregistré
            return $this->redirectToRoute("app_admin_author_show", ["id" => $author->getId()]);
        }

        return $this->render('admin/author/new.html.twig', [
            'form' => $form,
            "author_id" => $author != null ? $author->getId(): null //mettre l'id de l'auteur dans la variable author_id s'il s'agit d'une édition d'auteur à null sinon
        ]);
    }

    #[Route("/{id}", name: "app_admin_author_show",requirements: ["id" => "\d+"], methods: ["GET"])]
    public function show(?Author $author) : Response
    {
        return $this->render("admin/author/show.html.twig", [
            "author" => $author
        ]);
    }
    /*  ( ce qui est fait en haut) nous pouvons profiter de la puissance de Symfony et d’un objet appelé  EntityValueResolver  , qui va directement aller chercher notre entité en fonction de l’identifiant passé dans l’URL. Pour ce faire, remplacez votre argument  id  dans la méthode du controller par un argument typé de la classe de votre entité.

        public function show(int $id, AuthorRepository $repository) : Response
        {
            $author = $repository->find($id);
            return $this->render("admin/author/show.html.twig", [
                "author" => $author
            ]);
        }
    */


}
