<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\LogFilterQueryParamsDto;
use App\DTO\Response\CountItemDto;
use App\Repository\LogEntryRepository;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class LogAnalyticsController extends AbstractController
{
    public function __construct(
        private readonly LogEntryRepository $repository,
    ) {}

    #[OA\Get(
        path: '/count',
        description: 'Count all matching items in the logs',
        summary: 'Searches logs and provides aggregated count of matches',
        tags: ['analytics'],
        parameters: [
            new OA\Parameter(
                name: 'serviceNames[]',
                description: 'Array of service names',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string')
                )
            ),
            new OA\Parameter(
                name: 'statusCode',
                description: 'Filter on request status code',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'startDate',
                description: 'Start date',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
            new OA\Parameter(
                name: 'endDate',
                description: 'End date',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'date-time', format: 'date-time')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Count of matching results',
                content: new OA\JsonContent(
                    ref: new Model(type: CountItemDto::class)
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad input parameter',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(type: 'string')
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error'
            ),
        ]
    )]
    #[Route('/count', name: 'log_count', methods: ['GET'])]
    public function countLogs(
        #[MapQueryString(
            serializationContext: [
                DateTimeNormalizer::FORMAT_KEY => 'Y-m-d',
                DateTimeNormalizer::TIMEZONE_KEY => 'UTC',
            ]
        )]
        LogFilterQueryParamsDto $filterDto
    ): JsonResponse {
        try {
            $count = $this->repository->countFilteredLogEntries($filterDto);

            return $this->json(
                new CountItemDto($count)
            );
        } catch (\Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
