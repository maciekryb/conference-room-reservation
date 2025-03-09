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
        $entityManager->persist($conferenceRoom);
        $entityManager->flush();
    }

    public function delete(ConferenceRoom $conferenceRoom): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($conferenceRoom);
        $entityManager->flush();
    }
}
