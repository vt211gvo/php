<?php

namespace App\Repository;

use App\Entity\Guest;
use App\Services\Utility\PaginationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Guest>
 */
class GuestRepository extends ServiceEntityRepository
{
    private PaginationService $paginationService;

    public function __construct(
        ManagerRegistry   $registry,
        PaginationService $paginationService
    )
    {
        parent::__construct($registry, Guest::class);
        $this->paginationService = $paginationService;
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    #[ArrayShape([
        'guests' => "array",
        'totalPageCount' => "float",
        'totalItems' => "int"
    ])]
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('guest');

        if (!empty($data['firstName'])) {
            $queryBuilder->andWhere('guest.firstName LIKE :firstName')
                ->setParameter('firstName', '%' . $data['firstName'] . '%');
        }

        if (!empty($data['lastName'])) {
            $queryBuilder->andWhere('guest.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $data['lastName'] . '%');
        }

        if (!empty($data['email'])) {
            $queryBuilder->andWhere('guest.email LIKE :email')
                ->setParameter('email', '%' . $data['email'] . '%');
        }

        if (!empty($data['phone'])) {
            $queryBuilder->andWhere('guest.phone LIKE :phone')
                ->setParameter('phone', '%' . $data['phone'] . '%');
        }

        return $this->paginationService->paginate($queryBuilder, $itemsPerPage, $page);
    }

//    /**
//     * @return Guest[] Returns an array of Guest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Guest
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
