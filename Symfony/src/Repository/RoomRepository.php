<?php

namespace App\Repository;

use App\Entity\Room;
use App\Services\Utility\PaginationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    private PaginationService $paginationService;

    public function __construct(
        ManagerRegistry   $registry,
        PaginationService $paginationService
    )
    {
        parent::__construct($registry, Room::class);
        $this->paginationService = $paginationService;
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    #[ArrayShape([
        'rooms' => "array",
        'totalPageCount' => "float",
        'totalItems' => "int"
    ])]
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('room');

        if (!empty($data['name'])) {
            $queryBuilder->andWhere('room.name LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (!empty($data['description'])) {
            $queryBuilder->andWhere('room.description LIKE :description')
                ->setParameter('description', '%' . $data['description'] . '%');
        }

        if (!empty($data['type'])) {
            $queryBuilder->andWhere('room.type = :type')
                ->setParameter('type', $data['type']);
        }

        if (!empty($data['bedCount'])) {
            $queryBuilder->andWhere('room.bedCount = :bedCount')
                ->setParameter('bedCount', $data['bedCount']);
        }

        return $this->paginationService->paginate($queryBuilder, $itemsPerPage, $page);
    }

//    /**
//     * @return Room[] Returns an array of Room objects
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

//    public function findOneBySomeField($value): ?Room
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
