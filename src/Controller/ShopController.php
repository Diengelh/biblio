<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Shop;
use App\Enum\BookStatus;
use App\Repository\BorrowingRepository;
use App\Repository\ShopRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route("/shop")]
class ShopController extends AbstractController
{
    /**
     * displays all the borrowing of the current user
     */
    #[Route('/', name: 'app_shop_index')]
    public function index(
        ShopRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $shops = $paginator->paginate(
            $user->getShops(),
            $request->query->getInt("page", 1),
            3
        );

        return $this->render('shop/index.html.twig', [
            "shops" => $shops,
         ]);
    }

    /**
     * add then the book which the id is {id} to the shops of the current user
     */
    #[Route("/new/{id}", name:"app_shop_new", requirements: ["id" => "\d+"], methods: ["GET"])]
    public function new(
        ?Book $book, 
        EntityManagerInterface $manager,
        ShopRepository $shopRepository,
        BorrowingRepository $borrowingRepository
        ) {

            #TODO: 
                // faire de tel sorte que si un utilisateur a un emprunt dont 
                // la date de rendu est dépassé alors, l'utilisateur ne pourra
                // pas ajouté aucun article dans son panier.

        $shop = new Shop();
        
        $status = "danger";
        $message = "Le livre que vous tentez d'ajouter dans votre panier
                     est introuvable";

        if($book){
            //tester si l'utilisateur courant a déjà l'article dans son panier
            $ExistingShop = $shopRepository->findBy([
                "book" => $book, "user" => $this->getUser()
            ]); 
            if(!$ExistingShop) {
                
                //tester aussi si l'utilisateur courant à un emprunt en cours d'un exemplaire de ce livre
                $ExistingBorrowing = $borrowingRepository->findBy([
                    "book" => $book, "user" => $this->getUser()
                ]);

                if(!$ExistingBorrowing) {
                    //tester si le livre est disponible
                    if($book->getStatus() == BookStatus::Available){
                        $shop->setBook($book)
                            ->setUser($this->getUser())
                            ->setCreatedAt(new DateTimeImmutable());
                        
                            $manager->persist($shop);
                            $manager->flush();

                            $status = "success";
                            $message = "Le livre a été ajouté avec succès dans votre panier";
                    } else {

                        $message = "Le livre est Indisponible";
                    }
                } else {

                    $message = "Vous avez déjà un emprunt en cours de cet article";
                }
            } else {
                $status = "danger";
                $message = "L'article est déjà dans votre panier";
            }

        }
        
        $this->addFlash(
            $status,
            $message
        );
        
        return $this->redirectToRoute('app_book');
    }

    /**
     * delete the article which the id's value is {id}
     */
    #[Route("/delete/{id}", name: "app_shop_delete", requirements: ["id" => "\d+"])]
    public function delete(
        ?Shop $shop,
        EntityManagerInterface $manager
    ) 
    {
        
        $status = "danger";
        $message = "L'article que vous tentez de supprimer est introuvable";
        
        if($shop) {
             
            //restreindre l'accès si l'utilisateur n'est pas le propriétaire de l'article à supprimer
            $this->denyAccessUnlessGranted("shop.isCreator", $shop);

            $manager->remove($shop);
            $manager->flush();

            $status = "success";
            $message = "L'article a été supprimé avec succès";

        }   
        
        $this->addFlash(
            $status,
            $message
        );

        return $this->redirectToRoute('app_shop_index');
    }

    /**
     * delete all the shops of the current user
     */
    #[Route("/delete.all.shops", name: "app_shop_delete.all.shops")]
    public function deleleAllShops(EntityManagerInterface $manager)
    {
        /** @var USer $user */
        $user = $this->getUser();

        foreach ($user->getShops() as $shop) {
            $manager->remove($shop);
        }

        $manager->flush();  
        
        return $this->redirectToRoute('app_shop_index');
        
    }

}
