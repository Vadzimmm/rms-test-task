<?php

declare(strict_types=1);

namespace App\Command;

use App\LogSinker\Exception\FileNotReadableException;
use App\LogSinker\Parser\ParserInterface;
use App\LogSinker\Repository\LogEntryRepositoryInterface;
use App\LogSinker\Service\LogSinkerService;
use App\LogSinker\Stream\FileLineStream;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

final class LogSinkerCommand extends Command
{
    private const int DEFAULT_BATCH_SIZE = 5;

    /** @phpstan-ignore-next-line */
    protected static $defaultName = 'app:parse-log';

    public function __construct(
        private readonly LogEntryRepositoryInterface $logEntryRepository,
        private readonly ParserInterface $parser,
    ) {
        parent::__construct();
    }

    public static function getDefaultName(): string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Parses and persists log file entries.')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the log file')
            ->addArgument(
                'batchSize',
                InputArgument::OPTIONAL,
                'Batch size of logs (default: 5)',
                self::DEFAULT_BATCH_SIZE
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!is_string($filePath)) {
            $output->writeln('<error>Invalid file path argument.</error>');

            return Command::FAILURE;
        }

        if (!is_readable($filePath)) {
            throw new FileNotReadableException($filePath);
        }

        $batchSize = $input->getArgument('batchSize');
        if (!is_numeric($batchSize) || (int) $batchSize <= 0) {
            $output->writeln('<error>Batch size must be a positive integer.</error>');

            return Command::FAILURE;
        }

        $batchSize = (int) $batchSize;
        $logger = new ConsoleLogger($output);
        $lineStream = new FileLineStream($filePath);

        try {
            $logSinkerService = new LogSinkerService(
                $this->logEntryRepository,
                $this->parser,
                $logger
            );

            $logSinkerService->importFrom($lineStream, $batchSize);
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln('<info>Log file dispatched for processing.</info>');

        return Command::SUCCESS;
    }
}
