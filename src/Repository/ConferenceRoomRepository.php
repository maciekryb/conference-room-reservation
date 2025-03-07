<?php

namespace App\Repository;

use App\Entity\ConferenceRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConferenceRoom>
 */
class ConferenceRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConferenceRoom::class);
    }

    //    /**
    //     * @return ConferenceRoom[] Returns an array of ConferenceRoom objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function findOneBySomeField($value): ?ConferenceRoom
       {
           return $this->createQueryBuilder('c')
               ->andWhere('c.exampleField = :val')
               ->setParameter('val', $value)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }

       public function save(ConferenceRoom $conferenceRoom): void
       {
           $entityManager = $this->getEntityManager();
           $entityManager->persist($conferenceRoom);  // Przygotowuje obiekt do zapisu
           $entityManager->flush();  // Zatwierdza zmiany w bazie danych
       }
}
