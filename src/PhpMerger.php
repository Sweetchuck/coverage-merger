<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\PHP as PhpReporter;
use Symfony\Component\Console\Output\OutputInterface;

class PhpMerger extends PhpMergerBase
{

    protected OutputInterface $output;

    public function mergePhpFiles(\Iterator $phpFiles, OutputInterface $output)
    {
        return $this
            ->start($output)
            ->addPhpFiles($phpFiles)
            ->finish();
    }

    public function start(OutputInterface $output)
    {
        $this->output = $output;
        $driver = null;
        $filter = null;
        $this->coverage = new CodeCoverage($driver, $filter);

        return $this;
    }

    public function finish()
    {
        $writer = new PhpReporter();
        $this->output->write($writer->process($this->coverage));

        return $this;
    }
}
