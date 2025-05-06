<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\LogEntry\LogEntryEntity;
use App\Domain\LogSinker\LogEntry;
use App\Domain\LogSinker\Repository\LogEntryRepositoryInterface;
use App\Infrastructure\Repository\Filter\LogEntryFilterTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogEntryEntity>
 *
 *  Read/write methods in a single interface slightly violate ISP,
 *  but acceptable here due to app simplicity.
 */
class LogEntryRepository extends ServiceEntityRepository implements LogEntryRepositoryInterface
{
    use LogEntryFilterTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntryEntity::class);
    }

    public function countFilteredLogEntries(
        ?array $serviceNames = null,
        ?int $statusCode = null,
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $endDate = null,
    ): int {
        $qb = $this->createQueryBuilder('log');
        $this->applyServiceNamesFilter($qb, $serviceNames);
        $this->applyStatusCodeFilter($qb, $statusCode);
        $this->applyDateRangeFilter(
            $qb,
            $startDate,
            $endDate,
        );

        return (int) $qb
            ->select('COUNT(log.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function save(LogEntry ...$logEntries): void
    {
        foreach ($logEntries as $logEntry) {
            $logEntryEntity = new LogEntryEntity(
                $logEntry->serviceName,
                $logEntry->timestamp,
                $logEntry->requestLine,
                $logEntry->statusCode
            );
            $this->getEntityManager()->persist($logEntryEntity);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }
}
