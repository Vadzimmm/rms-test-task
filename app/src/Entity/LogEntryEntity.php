<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LogEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LogEntryRepository::class)]
#[ORM\Table(name: 'log_entry')]
class LogEntryEntity
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    /** @phpstan-ignore-next-line */
    private Uuid $id;

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $serviceName,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $timestamp,
        #[ORM\Column(type: 'string', length: 2048)]
        private string $requestLine,
        #[ORM\Column(type: 'integer')]
        private int $statusCode,
    ) {}

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): static
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getRequestLine(): string
    {
        return $this->requestLine;
    }

    public function setRequestLine(string $requestLine): static
    {
        $this->requestLine = $requestLine;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
