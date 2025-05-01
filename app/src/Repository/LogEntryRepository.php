<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Request\LogFilterQueryParamsDto;
use App\Entity\LogEntryEntity;
use App\Repository\Filter\LogEntryFilterTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogEntryEntity>
 */
class LogEntryRepository extends ServiceEntityRepository implements LogEntryRepositoryInterface
{
    use LogEntryFilterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntryEntity::class);
    }

    public function countFilteredLogEntries(LogFilterQueryParamsDto $filtersDto): int
    {
        $qb = $this->createQueryBuilder('log');
        $this->applyServiceNamesFilter($qb, $filtersDto->serviceNames);
        $this->applyStatusCodeFilter($qb, $filtersDto->statusCode);
        $this->applyDateRangeFilter(
            $qb,
            $filtersDto->startDate,
            $filtersDto->endDate,
        );

        return (int) $qb
            ->select('COUNT(log.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function save(LogEntryEntity $logEntryEntity): void
    {
        $this->getEntityManager()->persist($logEntryEntity);
        $this->getEntityManager()->flush();
    }
}
