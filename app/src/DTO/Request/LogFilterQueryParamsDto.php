<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class LogFilterQueryParamsDto
{
    /**
     * @param null|array<string> $serviceNames
     */
    public function __construct(
        #[Assert\Type('array')]
        #[Assert\All([new Assert\Type('string')])]
        public ?array $serviceNames = null,
        #[Assert\Type('integer')]
        #[Assert\PositiveOrZero]
        public ?int $statusCode = null,
        public ?\DateTimeImmutable $startDate = null,
        #[Assert\GreaterThanOrEqual(propertyPath: 'startDate')]
        public ?\DateTimeImmutable $endDate = null,
    ) {}
}
