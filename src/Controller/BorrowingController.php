<?php

namespace App\Controller;

use DateInterval;
use App\Entity\Book;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Borrowing;
use Doctrine\ORM\EntityManager;
use App\Repository\BorrowingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/borrowing")]
#[IsGranted("ROLE_USER")]
class BorrowingController extends AbstractController
{
    /**
     * displays all the borrowings of the current user.
     */
    #[Route('/', name: 'app_borrowing_index')]
    public function index(
        BorrowingRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $borrowings = $paginator->paginate(
            $repository->createQueryBuilder("b"),
            $request->query->getInt("page", 1),
            10
        );
        return $this->render('borrowing/index.html.twig', [
           "borrowings" => $borrowings,
        ]);
    }

    /**
     * delete a borrowing which it's id is {id}
     */
    #[Route("delete/{id}", name: "app_borrowing_delete", requirements: ["id" => "\d+"])]
    public function delete(
        ?Borrowing $borrowing,
        EntityManagerInterface $manager
    ){
        $status = "danger";
        $message = "Le livre que vous tentez de rendre est introuvable";

        if($borrowing){
            $manager->remove($borrowing);
            $manager->flush();

            $status = "success";
            $message = "Le livre a été rendu avec succès.";

        }

        $this->addFlash(
            $status,
            $message
        );

        return $this->redirectToRoute('app_borrowing_index');
    }

    /**
     * display the recapitulation of the shops of then current user.
     */
    #[Route("/recap.shops", name: "app_borrowing_recap_shop")]
    public function RecapShops(
    )
    {
        /** @var User $user */
        $user = $this->getUser();
        $borrowings = [];

        foreach ($user->GetShops() as $shop) {
            $borrowing = new Borrowing();
            $borrowing->setUser($user)
                    ->setBook($shop->getBook())
                    ->setBorrowingAt(new DateTimeImmutable())
                    ->setToRenderAt(
                        (new DateTimeImmutable())->add(new DateInterval("P15D"))
                    );

            $borrowings[] = $borrowing;
        }

        return $this->render("borrowing/recap.html.twig", [
            "borrowings" => $borrowings
        ]);

    }

    /**
     * valid the shop card of the current user.
     */
    #[Route("/validate.shops", name: "app_borrowing_validate.shops", requirements: ["id" => "\d+"])]
    public function ValidateShops(
        EntityManagerInterface $manager
    )
    {
        /** @var User $user */
        $user = $this->getUser();
        
        foreach ($user->GetShops() as $shop) {
            $borrowing = new Borrowing();
            $borrowing->setUser($user)
                    ->setBook($shop->getBook())
                    ->setBorrowingAt(new DateTimeImmutable())
                    ->setToRenderAt(
                        (new DateTimeImmutable())->add(new DateInterval("P15D"))
                    );

            $manager->persist($borrowing);
            $manager->remove($shop);
        }
        $manager->flush();

        $this->addFlash(
            "success",
            "Votre panier a été validé avec succès"
        );

        return $this->redirectToRoute('app_borrowing_index');
    
    }

}
