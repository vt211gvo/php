<?php

namespace App\Services\Utility;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginationService
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function paginate(QueryBuilder $queryBuilder, int $itemsPerPage, int $page): array
    {
        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'items' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems,
        ];
    }
}