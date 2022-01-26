<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Test\Unit;

use Codeception\Test\Unit;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReporter;
use Sweetchuck\CoverageMerger\CoverageMerger;
use Sweetchuck\CoverageMerger\Test\UnitTester;

/**
 * @covers \Sweetchuck\CoverageMerger\CoverageMerger
 */
class CoverageMergerTest extends Unit
{
    protected UnitTester $tester;

    public function testGetSet()
    {
        $merger = new CoverageMerger();
        $phpFiles = new \ArrayIterator([]);
        $coverage1 = $merger->merge($phpFiles);
        $merger->merge($phpFiles);
        $merger->setCoverage($coverage1);
        $this->tester->assertSame($coverage1, $merger->getCoverage());
    }

    public function testMergeSuccess()
    {
        $this->tester->runTestWithCodeCoverage();

        $realCase01Dir = codecept_data_dir('fixtures/case-01');
        $coverageFiles = new \ArrayIterator([
            '',
            new \SplFileInfo("$realCase01Dir/reports/A.php"),
            new \SplFileInfo("$realCase01Dir/reports/B.php"),
            new \SplFileInfo("$realCase01Dir/reports/C.php"),
        ]);

        $merger = new CoverageMerger();
        $coverage = $merger->merge($coverageFiles);

        $dstDir = "$realCase01Dir/reports/merged.html";
        $writer = new HtmlReporter();
        $writer->process($coverage, $dstDir);

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTMLFile("$dstDir/index.html");
        $xpath = new \DOMXPath($doc);
        /** @var string|\SplFileInfo $coverageFile */
        foreach ($coverageFiles as $coverageFile) {
            if ($coverageFile === '') {
                continue;
            }

            $baseName = $coverageFile->getBasename();
            $result = $xpath->query("//a[@href = '$baseName.html']");
            $this->tester->assertSame(1, $result->length, "link to $baseName on dashboard");
            $link = $result->item(0);
            $tableRow = $xpath->query('./ancestor::tr', $link)->item(0);
            $resultCell = $xpath->query('./td[position() = 3]', $tableRow)->item(0);

            $this->tester->assertSame(
                '100.00%',
                $resultCell->textContent,
                "$baseName is merged into the final report",
            );
        }
    }

    public function testMergeFail()
    {
        $fileContent = '<?php return new \stdClass();';
        $fileName = tempnam(sys_get_temp_dir(), 'coverage-merger-2.x-');
        file_put_contents($fileName, $fileContent);
        $phpFiles = new \ArrayIterator([$fileName]);
        $merger = new CoverageMerger();
        $this->tester->expectThrowable(
            \RuntimeException::class,
            function () use ($merger, $phpFiles) {
                $merger->merge($phpFiles);
            }
        );
    }
}
