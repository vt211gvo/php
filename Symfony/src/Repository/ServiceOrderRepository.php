<?php

namespace App\Repository;

use App\Entity\ServiceOrder;
use App\Services\Utility\PaginationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<ServiceOrder>
 */
class ServiceOrderRepository extends ServiceEntityRepository
{
    private PaginationService $paginationService;

    public function __construct(
        ManagerRegistry   $registry,
        PaginationService $paginationService
    )
    {
        parent::__construct($registry, ServiceOrder::class);
        $this->paginationService = $paginationService;
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    #[ArrayShape([
        'serviceOrders' => "array",
        'totalPageCount' => "float",
        'totalItems' => "int"
    ])]
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('so');

        if (!empty($data['serviceType'])) {
            $queryBuilder->andWhere('so.serviceType LIKE :serviceType')
                ->setParameter('serviceType', '%' . $data['serviceType'] . '%');
        }

        if (!empty($data['status'])) {
            $queryBuilder->andWhere('so.status = :status')
                ->setParameter('status', $data['status']);
        }

        if (!empty($data['clientName'])) {
            $queryBuilder->andWhere('so.clientName LIKE :clientName')
                ->setParameter('clientName', '%' . $data['clientName'] . '%');
        }

        if (!empty($data['createdAfter'])) {
            $queryBuilder->andWhere('so.createdAt >= :createdAfter')
                ->setParameter('createdAfter', $data['createdAfter']);
        }

        if (!empty($data['createdBefore'])) {
            $queryBuilder->andWhere('so.createdAt <= :createdBefore')
                ->setParameter('createdBefore', $data['createdBefore']);
        }

        return $this->paginationService->paginate($queryBuilder, $itemsPerPage, $page);
    }

//    /**
//     * @return ServiceOrder[] Returns an array of ServiceOrder objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ServiceOrder
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
