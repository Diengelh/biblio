<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/editor')]
class EditorController extends AbstractController
{
    #[Route('', name: 'app_admin_editor_index', methods: ["GET"])]
    public function index(
        Request $request,
        EditorRepository $repository,
        PaginatorInterface $paginator ): Response
    {
        $query = $repository->createQueryBuilder("e");

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('admin/editor/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[IsGranted("ROLE_AJOUT_DE_LIVRE")]
    #[Route('/{id}/edit', name: "app_admin_editor_edit", requirements: ["id" => "\d+"], methods: ["GET", "POST"])]
    #[Route('/new', name: 'app_admin_editor_new', methods: ["GET", "POST"])]
    public function new(?Editor $editor, Request $request, EntityManagerInterface $manager): Response
    {
        if($editor){
            // s'il s'agit d'une éditon de livre
            $this->denyAccessUnlessGranted("ROLE_EDITION_DE_LIVRE");
        }

        $editor ??= new Editor();
        $form = $this->createForm(EditorType::class, $editor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //envoyer les données de l'objet editor dans la base de données qui été rempli dans le formulaire
            $manager->persist($editor);
            $manager->flush();
            
            //redirection vers le formulaire
            return $this->redirectToRoute("app_admin_editor_index");
        }

        return $this->render('admin/editor/new.html.twig', [
            'form' => $form,
            'editor_id' => $editor != null ? $editor->getId(): null,
        ]);
    }
}
