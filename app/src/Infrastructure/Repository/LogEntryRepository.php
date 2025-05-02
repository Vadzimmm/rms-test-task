<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\LogEntry\LogEntryEntity;
use App\Infrastructure\Repository\Filter\LogEntryFilterTrait;
use App\Shared\DTO\Request\LogFilterQueryParamsDto;
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

    public function save(LogEntryEntity ...$logEntryEntities): void
    {
        foreach ($logEntryEntities as $logEntryEntity) {
            $this->getEntityManager()->persist($logEntryEntity);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }
}
