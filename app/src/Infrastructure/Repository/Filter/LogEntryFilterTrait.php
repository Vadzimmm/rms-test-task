<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Filter;

use Doctrine\ORM\QueryBuilder;

trait LogEntryFilterTrait
{
    /**
     * @param null|array<string> $serviceNames
     */
    public function applyServiceNamesFilter(QueryBuilder $qb, ?array $serviceNames): QueryBuilder
    {
        $alias = $this->getRootAlias($qb);

        if (!empty($serviceNames)) {
            $qb->andWhere("{$alias}.serviceName IN (:serviceNames)")
                ->setParameter('serviceNames', $serviceNames)
            ;
        }

        return $qb;
    }

    public function applyStatusCodeFilter(QueryBuilder $qb, ?int $statusCode): QueryBuilder
    {
        $alias = $this->getRootAlias($qb);

        if (null !== $statusCode) {
            $qb->andWhere("{$alias}.statusCode = :statusCode")
                ->setParameter('statusCode', $statusCode)
            ;
        }

        return $qb;
    }

    public function applyDateRangeFilter(
        QueryBuilder $qb,
        ?\DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate
    ): QueryBuilder {
        $alias = $this->getRootAlias($qb);

        if (null !== $startDate) {
            $qb->andWhere("{$alias}.timestamp >= :startDate")
                ->setParameter('startDate', $startDate)
            ;
        }

        if (null !== $endDate) {
            $qb->andWhere("{$alias}.timestamp <= :endDate")
                ->setParameter('endDate', $endDate)
            ;
        }

        return $qb;
    }

    private function getRootAlias(QueryBuilder $qb): string
    {
        $rootAliases = $qb->getRootAliases();

        return $rootAliases[0];
    }
}
