<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\LogEntryEntity;
use App\LogSinker\FileReader;
use App\LogSinker\LogParser;
use App\LogSinker\RegexParserStrategyInterface;
use App\Repository\LogEntryRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogSinkerCommand extends Command
{
    /** @phpstan-ignore-next-line */
    protected static $defaultName = 'app:parse-log';

    public function __construct(
        private readonly LogEntryRepositoryInterface $logEntryRepository,
        private readonly RegexParserStrategyInterface $parserStrategy
    ) {
        parent::__construct();
    }

    public static function getDefaultName(): string
    {
        return static::$defaultName;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Parses log files and outputs structured log entries.')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the log file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!is_string($filePath)) {
            $output->writeln('<error>Invalid file path argument.</error>');

            return Command::FAILURE;
        }

        try {
            $reader = new FileReader($filePath);
            $logParser = new LogParser($reader, $this->parserStrategy);

            foreach ($logParser->parse() as $entry) {
                $entity = new LogEntryEntity();
                $entity->setRequestLine($entry->requestLine);
                $entity->setServiceName($entry->serviceName);
                $entity->setTimestamp($entry->timestamp);
                $entity->setStatusCode($entry->statusCode);
                $this->logEntryRepository->save($entity);

                $output->writeln(sprintf(
                    '[%s] %s: %s (Status: %d)',
                    $entry->timestamp->format('Y-m-d H:i:s'),
                    $entry->serviceName,
                    $entry->requestLine,
                    $entry->statusCode
                ));
            }
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln('<info>Log parsing completed successfully.</info>');

        return Command::SUCCESS;
    }
}
