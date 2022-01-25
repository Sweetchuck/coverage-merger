<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReporter;

class PhpMergerHtmlOut extends PhpMergerBase
{

    public function mergePhpFiles(
        \Iterator $phpFiles,
        string $destination,
        int $lowUpperBound = 50,
        int $highLowerBound = 90,
        string $generator = ''
    ) {
        return $this
            ->start()
            ->addPhpFiles($phpFiles)
            ->finish($destination, $lowUpperBound, $highLowerBound, $generator);
    }

    public function start()
    {
        $driver = null;
        $filter = null;
        $this->coverage = new CodeCoverage($driver, $filter);

        return $this;
    }

    /**
     * @return $this
     *
     * @see \SebastianBergmann\CodeCoverage\Report\Html\Facade::__construct
     */
    public function finish(
        string $destination,
        int $lowUpperBound = 50,
        int $highLowerBound = 90,
        string $generator = ''
    ) {
        $writer = new HtmlReporter($lowUpperBound, $highLowerBound, $generator);
        $writer->process($this->coverage, $destination);

        return $this;
    }
}
