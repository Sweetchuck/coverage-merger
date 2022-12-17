<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\PHP;

class CoverageMerger implements CoverageMergerInterface
{
    protected ?CodeCoverage $coverage = null;

    public function getCoverage(): ?CodeCoverage
    {
        return $this->coverage;
    }

    /**
     * @return $this
     */
    public function setCoverage(CodeCoverage $coverage)
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

    public function start()
    {
        $this->coverage = $this->creteCodeCoverage();

        return $this;
    }

    /**
     * @return $this
     */
    public function addPhpFiles(\Iterator $phpFiles)
    {
        while ($phpFiles->valid()) {
            $this->addPhpFile($phpFiles->current());
            $phpFiles->next();
        }

        return $this;
    }

    /**
     * @param string|\SplFileInfo $phpFile
     *
     * @return $this
     */
    public function addPhpFile($phpFile)
    {
        $filename = $phpFile instanceof \SplFileInfo ? $phpFile->getPathname() : rtrim($phpFile, "\r\n");
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
        $filter = null;
        $driver = null;

        return new CodeCoverage($driver, $filter);
    }

    /**
     * @return $this
     */
    protected function normalizeCoverage(CodeCoverage $coverage)
    {
        $tests = $coverage->getTests();
        foreach ($tests as &$test) {
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

    protected function requireFile(string $filename)
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
