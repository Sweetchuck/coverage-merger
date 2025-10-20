<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger;

use SebastianBergmann\CodeCoverage\CodeCoverage;

interface CoverageMergerInterface
{

    public function getCoverage(): ?CodeCoverage;

    public function setCoverage(CodeCoverage $coverage): static;

    public function merge(\Iterator $phpFiles): CodeCoverage;

    public function start(): static;

    public function addPhpFiles(\Iterator $phpFiles): static;

    public function addPhpFile(string|\SplFileInfo $phpFile): static;

    public function getFileContent(): ?string;
}
