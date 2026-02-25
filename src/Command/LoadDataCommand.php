<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\AI\Store\Document\VectorizerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Pgvector\Vector;

#[AsCommand(
    name: 'app:data:load',
    description: 'Load product data from JSON files with smart file selection',
)]
class LoadDataCommand extends Command
{
    private const DATA_DIR = __DIR__ . '/../../data';

    private array $jsonFiles = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private VectorizerInterface $ollama
    ) {
        parent::__construct();
        $this->loadAvailableFiles();
    }

    private function loadAvailableFiles(): void
    {
        $files = scandir(self::DATA_DIR);
        $this->jsonFiles = array_values(array_filter($files, fn($f) => str_ends_with($f, '.json')));
        sort($this->jsonFiles);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'files',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'File numbers to load (1-based index, or "all" for all files)'
            )
            ->addOption(
                'list',
                'l',
                InputOption::VALUE_NONE,
                'List all available files with their numbers'
            )
            ->addOption(
                'skip-embedding',
                null,
                InputOption::VALUE_NONE,
                'Skip embedding generation (faster, for testing)'
            )
            ->addOption(
                'truncate',
                null,
                InputOption::VALUE_NONE,
                'Truncate product table before loading'
            )
            ->addOption(
                'batch-size',
                null,
                InputOption::VALUE_REQUIRED,
                'Batch size for flushing entities',
                '50'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // List files if requested
        if ($input->getOption('list')) {
            $this->listFiles($io);
            return Command::SUCCESS;
        }

        $fileArgs = $input->getArgument('files');
        $skipEmbedding = $input->getOption('skip-embedding');
        $truncate = $input->getOption('truncate');
        $batchSize = (int) $input->getOption('batch-size');

        // Determine which files to load
        $filesToLoad = $this->resolveFiles($fileArgs, $io);

        if (empty($filesToLoad)) {
            $io->error('No files selected. Use --list to see available files.');
            return Command::FAILURE;
        }

        // Truncate if requested
        if ($truncate) {
            $io->section('Truncating product table');
            $connection = $this->entityManager->getConnection();
            $connection->executeStatement('TRUNCATE TABLE product');
            $io->success('Product table truncated');
        }

        $io->title('Loading Product Data');
        $io->text(sprintf('Files to load: <info>%d</info>', count($filesToLoad)));
        $io->text(sprintf('Skip embedding: <info>%s</info>', $skipEmbedding ? 'yes' : 'no'));
        $io->newLine();

        $totalLoaded = 0;
        $totalSkipped = 0;

        foreach ($filesToLoad as $index => $filename) {
            $io->section(sprintf('[%d/%d] Loading: %s', $index + 1, count($filesToLoad), $filename));

            $result = $this->loadProductsFromFile($io, $filename, $skipEmbedding, $batchSize);
            $totalLoaded += $result['loaded'];
            $totalSkipped += $result['skipped'];

            $io->newLine();
        }

        $io->success(sprintf(
            'Loaded %d products (%d skipped as duplicates)',
            $totalLoaded,
            $totalSkipped
        ));

        return Command::SUCCESS;
    }

    /**
     * @param array<string> $fileArgs
     * @return array<string>
     */
    private function resolveFiles(array $fileArgs, SymfonyStyle $io): array
    {
        // If no arguments, show interactive selection
        if (empty($fileArgs)) {
            return $this->interactiveSelection($io);
        }

        // Check for "all" keyword
        if (in_array('all', $fileArgs, true) || in_array('*', $fileArgs, true)) {
            return $this->jsonFiles;
        }

        $filesToLoad = [];

        foreach ($fileArgs as $arg) {
            // Handle ranges (e.g., "1-5")
            if (str_contains($arg, '-')) {
                [$start, $end] = explode('-', $arg);
                $start = (int) trim($start);
                $end = (int) trim($end);

                for ($i = $start; $i <= $end; $i++) {
                    if (isset($this->jsonFiles[$i - 1])) {
                        $filesToLoad[] = $this->jsonFiles[$i - 1];
                    }
                }
                continue;
            }

            // Handle single number
            $index = (int) $arg;
            if ($index > 0 && isset($this->jsonFiles[$index - 1])) {
                $filesToLoad[] = $this->jsonFiles[$index - 1];
            } elseif ($index > 0) {
                $io->warning("File #{$index} does not exist");
            }
        }

        // Remove duplicates while preserving order
        return array_values(array_unique($filesToLoad));
    }

    /**
     * @return array<string>
     */
    private function interactiveSelection(SymfonyStyle $io): array
    {
        $this->listFiles($io);

        $answer = $io->ask(
            'Enter file numbers to load (e.g., "1,3,5" or "1-5" or "all")',
            'all'
        );

        if ($answer === 'all') {
            return $this->jsonFiles;
        }

        return $this->resolveFiles(explode(',', $answer), $io);
    }

    private function listFiles(SymfonyStyle $io): void
    {
        $io->title('Available Data Files');

        $rows = [];
        foreach ($this->jsonFiles as $index => $filename) {
            $filePath = self::DATA_DIR . '/' . $filename;
            $size = filesize($filePath);
            $sizeStr = sprintf('%.1f KB', $size / 1024);

            // Try to count items in JSON
            $count = $this->countJsonItems($filePath);

            $rows[] = [
                sprintf('<info>%3d</info>', $index + 1),
                str_replace('.json', '', $filename),
                $count > 0 ? $count . ' items' : 'unknown',
                $sizeStr,
            ];
        }

        $io->table(['#', 'Name', 'Items', 'Size'], $rows);
        $io->text([
            '',
            'Usage examples:',
            '  <info>php bin/console app:data:load 1</info>           - Load file #1',
            '  <info>php bin/console app:data:load 1-5</info>         - Load files 1 through 5',
            '  <info>php bin/console app:data:load all</info>         - Load all files',
            '  <info>php bin/console app:data:load -l</info>          - List files only',
            '  <info>php bin/console app:data:load --truncate</info>  - Truncate table first',
        ]);
    }

    private function countJsonItems(string $filePath): int
    {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        return is_array($data) ? count($data) : 0;
    }

    /**
     * @return array{loaded: int, skipped: int}
     */
    private function loadProductsFromFile(
        SymfonyStyle $io,
        string $filename,
        bool $skipEmbedding,
        int $batchSize
    ): array {
        $jsonPath = self::DATA_DIR . '/' . $filename;

        if (!file_exists($jsonPath)) {
            $io->error("File not found: {$jsonPath}");
            return ['loaded' => 0, 'skipped' => 0];
        }

        $jsonContent = file_get_contents($jsonPath);
        $products = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $io->error('Failed to parse JSON: ' . json_last_error_msg());
            return ['loaded' => 0, 'skipped' => 0];
        }

        $loaded = 0;
        $skipped = 0;

        $io->progressStart(count($products));

        foreach ($products as $data) {
            // Check for duplicates
            $existing = $this->entityManager->getRepository(Product::class)->findOneBy([
                'name' => $data['name'],
                'category' => $data['category'],
            ]);

            if ($existing) {
                $skipped++;
                $io->progressAdvance();
                continue;
            }

            $product = new Product();
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setCategory($data['category']);
            $product->setTags($data['tags'] ?? []);
            $product->setInStock($data['inStock'] ?? true);

            // Generate embedding
            if (!$skipEmbedding) {
                try {
                    $vectorResult = $this->ollama->vectorize(
                        values: $product->getSearchableText(),
                        options: ['dimensions' => 1536]
                    );
                    $embedding = new Vector($vectorResult->getData());
                    $product->setEmbedding($embedding);
                } catch (\Exception $e) {
                    $io->error("Embedding failed: " . $e->getMessage());
                    continue;
                }
            }

            $this->entityManager->persist($product);
            $loaded++;

            // Batch flush
            if ($loaded % $batchSize === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }

            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
        $io->progressFinish();

        return [
            'loaded' => $loaded,
            'skipped' => $skipped,
        ];
    }
}
