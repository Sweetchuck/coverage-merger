<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger;

use SebastianBergmann\CodeCoverage\CodeCoverage;

interface CoverageMergerInterface
{

    public function getCoverage(): ?CodeCoverage;

    /**
     * @return $this
     */
    public function setCoverage(CodeCoverage $coverage);

    public function merge(\Iterator $phpFiles): CodeCoverage;

    public function start();

    /**
     * @return $this
     */
    public function addPhpFiles(\Iterator $phpFiles);

    /**
     * @param string|\SplFileInfo $phpFile
     *
     * @return $this
     */
    public function addPhpFile($phpFile);

    public function getFileContent(): ?string;
}
