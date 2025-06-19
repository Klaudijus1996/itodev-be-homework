<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAvailable(
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $search = null
    ): array
    {
        $query = $this->createQueryBuilder('e')
            ->andWhere('e.available_spots > 0')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if (!empty($search)) {
            $query
                ->andWhere('e.name LIKE :search OR e.date LIKE :search OR e.location LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if (empty($orderBy)) {
            $orderBy = [
                'date' => 'ASC',
                'name' => 'ASC',
            ];
        }

        foreach ($orderBy as $field => $order) {
            if (!in_array($field, [
                'date',
                'name',
                'location',
                'available_spots',
                'created_at',
            ])) {
                continue;
            }

            $query->addOrderBy("e.{$field}", $order);
        }

        return $query->getQuery()->getResult();
    }

    public function countAvailable(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.available_spots > 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
