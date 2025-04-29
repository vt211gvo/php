<?php

namespace App\Repository;

use App\Entity\Review;
use App\Services\Utility\PaginationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    private PaginationService $paginationService;

    public function __construct(
        ManagerRegistry   $registry,
        PaginationService $paginationService
    )
    {
        parent::__construct($registry, Review::class);
        $this->paginationService = $paginationService;
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    #[ArrayShape([
        'reviews' => "array",
        'totalPageCount' => "float",
        'totalItems' => "int"
    ])]
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('review');

        if (!empty($data['rating'])) {
            $queryBuilder->andWhere('review.rating = :rating')
                ->setParameter('rating', $data['rating']);
        }

        if (!empty($data['comment'])) {
            $queryBuilder->andWhere('review.comment LIKE :comment')
                ->setParameter('comment', '%' . $data['comment'] . '%');
        }

        if (!empty($data['createdAt'])) {
            $queryBuilder->andWhere('DATE(review.createdAt) = :createdAt')
                ->setParameter('createdAt', $data['createdAt']);
        }

        if (!empty($data['guestId'])) {
            $queryBuilder->andWhere('review.guest = :guestId')
                ->setParameter('guestId', $data['guestId']);
        }

        return $this->paginationService->paginate($queryBuilder, $itemsPerPage, $page);
    }

//    /**
//     * @return Review[] Returns an array of Review objects
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

//    public function findOneBySomeField($value): ?Review
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
