<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\LogEntry\LogEntryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class LogEntryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $entries = [
            ['USER-SERVICE', '2018-01-01 10:00:00', 'POST /users HTTP/1.1', 201],
            ['USER-SERVICE', '2018-01-02 11:00:00', 'POST /users HTTP/1.1', 201],
            ['USER-SERVICE', '2021-05-01 09:00:00', 'POST /users HTTP/1.1', 400],
            ['USER-SERVICE', '2021-05-02 10:30:00', 'POST /users HTTP/1.1', 400],
            ['INVOICE-SERVICE', '2021-05-01 12:00:00', 'POST /invoices HTTP/1.1', 201],
            ['INVOICE-SERVICE', '2021-05-03 13:45:00', 'POST /invoices HTTP/1.1', 201],
            ['USER-SERVICE', '2025-04-01 08:00:00', 'POST /users HTTP/1.1', 500],
            ['INVOICE-SERVICE', '2025-04-01 09:30:00', 'POST /invoices HTTP/1.1', 400],
            ['INVOICE-SERVICE', '2025-04-01 10:45:00', 'POST /invoices HTTP/1.1', 201],
        ];

        foreach ($entries as [$service, $datetime, $request, $status]) {
            $entry = new LogEntryEntity(
                $service,
                new \DateTimeImmutable($datetime),
                $request,
                $status
            );
            $manager->persist($entry);
        }

        $manager->flush();
    }
}
