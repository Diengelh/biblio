<?php

namespace App\Repository;

use App\Entity\Author;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * findByDateOfBirth:
     * prend en paramètre un tableau contenant éventuellement une
     * date de début et une date de fin et renvoie les auteurs nés après la
     * date de début, avant la date de fin ou entre les deux dates selon
     * que seule la date de début est définie, seule la date de fin est définie
     * ou toutes les deux dates sont définies.
     */
    public function findByDateOfBirth(array $dates=[])
    {
        $qd = $this->createQueryBuilder('a');

        if(\array_key_exists("start", $dates)){
            $qd->andWhere("a.dateOfBirth >= :start")
               ->setParameter("start", new DateTimeImmutable($dates["start"]));
        }

        if(\array_key_exists("end", $dates)){
            $qd->andWhere("a.dateOfBirth <= :end")
               ->setParameter("end", new DateTimeImmutable($dates["end"]));
        }

        return  $qd -> orderBy("a.dateOfBirth", "DESC")
                    -> getQuery();
        }
}
