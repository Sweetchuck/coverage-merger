<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector as DriverSelector;
use SebastianBergmann\CodeCoverage\Filter as CodeCoverageFilter;
use SebastianBergmann\CodeCoverage\Report\PHP;

class CoverageMerger implements CoverageMergerInterface
{
    protected ?CodeCoverage $coverage = null;

    public function getCoverage(): ?CodeCoverage
    {
        return $this->coverage;
    }

    public function setCoverage(CodeCoverage $coverage): static
    {
        $this->coverage = $coverage;

        return $this;
    }

    public function merge(\Iterator $phpFiles): CodeCoverage
    {
        return $this
            ->start()
            ->addPhpFiles($phpFiles)
            ->getCoverage();
    }

    public function start(): static
    {
        $this->coverage = $this->creteCodeCoverage();

        return $this;
    }

    public function addPhpFiles(\Iterator $phpFiles): static
    {
        while ($phpFiles->valid()) {
            $this->addPhpFile($phpFiles->current());
            $phpFiles->next();
        }

        return $this;
    }

    public function addPhpFile(string|\SplFileInfo $phpFile): static
    {
        $filename = $phpFile instanceof \SplFileInfo ?
            $phpFile->getPathname()
            : rtrim($phpFile, "\r\n");

        $filename = $this->prepareInputFilename($filename);
        if ($filename === '') {
            return $this;
        }

        $coverage = $this->requireCoverage($filename);
        $this->normalizeCoverage($coverage);
        $this->coverage->merge($coverage);

        return $this;
    }

    public function getFileContent(): ?string
    {
        $coverage = $this->getCoverage();

        return $coverage ? (new PHP)->process($coverage) . "\n" : null;
    }

    protected function creteCodeCoverage(): CodeCoverage
    {
        $filter = new CodeCoverageFilter();
        $driver = (new DriverSelector())->forLineCoverage($filter);

        return new CodeCoverage($driver, $filter);
    }

    protected function normalizeCoverage(CodeCoverage $coverage): static
    {
        $tests = $coverage->getTests();
        foreach ($tests as &$test) {
            // @phpstan-ignore-next-line
            $test['fromTestcase'] = $test['fromTestcase'] ?? false;
        }
        $coverage->setTests($tests);

        return $this;
    }

    protected function prepareInputFilename(string $filename): string
    {
        return preg_replace(
            '@^/proc/self/fd/(?P<id>\d+)$@',
            'php://fd/$1',
            $filename,
        );
    }

    protected function requireFile(string $filename): mixed
    {
        return require $filename;
    }

    protected function requireCoverage(string $filename): CodeCoverage
    {
        $coverage = $this->requireFile($filename);
        if (!($coverage instanceof CodeCoverage)) {
            throw new \RuntimeException(sprintf(
                "%s doesn't return a valid %s object!",
                $filename,
                CodeCoverage::class,
            ));
        }

        return $coverage;
    }
}
