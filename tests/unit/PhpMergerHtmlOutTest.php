<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Test\Unit;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;
use Sweetchuck\CoverageMerger\PhpMergerHtmlOut;
use Sweetchuck\CoverageMerger\Test\UnitTester;

/**
 * @covers \Sweetchuck\CoverageMerger\PhpMergerHtmlOut<extended>
 */
class PhpMergerHtmlOutTest extends Unit
{
    protected UnitTester $tester;
    
    public function testMergePhpFiles()
    {
        $realCase01Dir = codecept_data_dir('fixtures/case-01');
        $vfs = vfsStream::setup(
            'root',
            0777,
            [
                'case-01' => [
                    'coverage' => [],
                    'src' => [],
                ],
                'actual' => [],
            ],
        );
        $vfsCase01Dir = $vfs->url() . '/case-01';

        $replacementPairs = [
            '{{ baseDir }}' => $vfsCase01Dir,
        ];

        $files = [
            'coverage/coverage.a.php',
            'coverage/coverage.b.php',
            'coverage/coverage.c.php',
            'src/a.php',
            'src/b.php',
            'src/c.php',
        ];
        foreach ($files as $file) {
            file_put_contents(
                "$vfsCase01Dir/$file",
                strtr(
                    file_get_contents("$realCase01Dir/$file"),
                    $replacementPairs
                )
            );
        }

        $coverageFiles = new \ArrayIterator([
            new \SplFileInfo("$vfsCase01Dir/coverage/coverage.a.php"),
            new \SplFileInfo("$vfsCase01Dir/coverage/coverage.b.php"),
            new \SplFileInfo("$vfsCase01Dir/coverage/coverage.c.php"),
        ]);

        $destination = $vfs->url() . '/actual';
        $merger = new PhpMergerHtmlOut();
        $merger->mergePhpFiles($coverageFiles, $destination);
        $this->tester->assertFileExists("$destination/index.html");
    }
}
