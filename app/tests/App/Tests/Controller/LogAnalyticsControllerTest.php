<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\LogAnalyticsController;
use App\DataFixtures\LogEntryFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LogAnalyticsController::class)]
final class LogAnalyticsControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();

        $this->client = self::createClient();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);

        $this->loadFixtures([new LogEntryFixture()]);
    }

    #[DataProvider('provideFiltersAndExpectedCount')]
    public function testCountEndpointWithFilters(array $query, int $expectedCount): void
    {
        $this->client->request('GET', '/count', $query);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('counter', $data);
        $this->assertSame($expectedCount, $data['counter']);
    }

    public static function provideFiltersAndExpectedCount(): array
    {
        return [
            'USER-SERVICE with 201' => [
                ['serviceNames' => ['USER-SERVICE'], 'statusCode' => 201],
                2,
            ],
            'INVOICE-SERVICE with 201' => [
                ['serviceNames' => ['INVOICE-SERVICE'], 'statusCode' => 201],
                3,
            ],
            'All logs in 2021' => [
                ['startDate' => '2021-01-01T00:00:00', 'endDate' => '2021-12-31T23:59:59'],
                4,
            ],
            'USER-SERVICE 400 in 2021' => [
                [
                    'serviceNames' => ['USER-SERVICE'],
                    'statusCode' => 400,
                    'startDate' => '2021-01-01T00:00:00',
                    'endDate' => '2021-12-31T23:59:59',
                ],
                2,
            ],
            'USER-SERVICE 500 in 2025' => [
                [
                    'serviceNames' => ['USER-SERVICE'],
                    'statusCode' => 500,
                    'startDate' => '2025-01-01T00:00:00',
                ],
                1,
            ],
        ];
    }

    private function loadFixtures(array $fixtures): void
    {
        $loader = new Loader();

        foreach ($fixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $executor->execute($loader->getFixtures());
    }
}
