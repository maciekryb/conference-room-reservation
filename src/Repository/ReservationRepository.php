<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $reservation): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($reservation);
        $entityManager->flush();
    }

    // Sprawdzamy, czy sala jest dostępna w danym przedziale czasowym
    public function findByRoomAndTime($roomId, $startTime, $endTime): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->where('r.conference_room_id = :room')  // Odwołujemy się do kolumny conference_room_id
            ->andWhere('r.start_time < :endTime')  // Rezerwacja zaczyna się przed końcem nowej rezerwacji
            ->andWhere('r.end_time > :startTime')  // Rezerwacja kończy się po rozpoczęciu nowej rezerwacji
            ->setParameter('room', $roomId) 
            ->setParameter('startTime', $startTime)  // Używamy poprawnej nazwy parametru
            ->setParameter('endTime', $endTime)  // Używamy poprawnej nazwy parametru
            ->getQuery()
            ->getOneOrNullResult();  // Zwracamy jedną rezerwację lub null, jeśli nie ma konfliktu
    }
    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
