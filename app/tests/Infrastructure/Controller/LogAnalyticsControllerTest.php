<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controller;

use App\Application\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 *
 * @coversNothing
 */
final class LogAnalyticsControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testCountLogsReturnsCorrectCount(): void
    {
        $queryBusMock = $this->createMock(QueryBusInterface::class);
        $queryBusMock->expects($this->once())
            ->method('execute')
            ->willReturn(42)
        ;

        self::getContainer()->set(QueryBusInterface::class, $queryBusMock);

        $this->client->request('GET', '/count', [
            'serviceNames' => ['USER-SERVICE'],
            'statusCode' => 201,
        ]);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('counter', $data);
        $this->assertSame(42, $data['counter']);
    }
}
