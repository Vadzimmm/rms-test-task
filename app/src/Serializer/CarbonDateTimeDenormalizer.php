<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Exception\InvalidDateTimeFormat;
use Carbon\Carbon;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CarbonDateTimeDenormalizer implements DenormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return \DateTimeImmutable::class === $type && is_string($data);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws InvalidDateTimeFormat
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): \DateTimeImmutable
    {
        if (!is_string($data)) {
            throw new InvalidDateTimeFormat('Expected string for datetime, got '.gettype($data));
        }

        try {
            $carbon = new Carbon($data);

            return \DateTimeImmutable::createFromMutable($carbon);
        } catch (\Throwable $e) {
            throw new InvalidDateTimeFormat('Invalid datetime format: "'.$data.'"', 0, $e);
        }
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \DateTimeImmutable::class => true,
        ];
    }
}
