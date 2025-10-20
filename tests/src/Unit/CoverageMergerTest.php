<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReporter;
use Sweetchuck\CoverageMerger\CoverageMerger;

#[CoversClass(CoverageMerger::class)]
class CoverageMergerTest extends TestCase
{
    #[Test]
    public function testGetSet(): void
    {
        $merger = new CoverageMerger();
        $phpFiles = new \ArrayIterator([]);
        $coverage1 = $merger->merge($phpFiles);
        $merger->merge($phpFiles);
        $merger->setCoverage($coverage1);
        static::assertSame($coverage1, $merger->getCoverage());
    }

    #[Test]
    public function testMergeSuccess(): void
    {
        $this->runTestWithCodeCoverage();

        $fixturesDir = $this->getFixturesDir();
        $realCase01Dir = "$fixturesDir/case-01";
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
        foreach ($coverageFiles as $coverageFile) {
            if ($coverageFile === '') {
                continue;
            }

            $baseName = $coverageFile->getBasename();
            /** @var false|\DOMNodeList<\DOMElement> $result */
            $result = $xpath->query("//a[@href = '$baseName.html']");
            static::assertInstanceOf(\DOMNodeList::class, $result);
            static::assertCount(1, $result, "link to $baseName on dashboard");
            $link = $result->item(0);
            $tableRowList = $xpath->query('./ancestor::tr', $link);
            static::assertInstanceOf(\DOMNodeList::class, $tableRowList);
            $tableRow = $tableRowList->item(0);
            static::assertInstanceOf(\DOMElement::class, $tableRow);

            $resultCellList = $xpath->query('./td[position() = 3]', $tableRow);
            static::assertInstanceOf(\DOMNodeList::class, $resultCellList);
            $resultCell = $resultCellList->item(0);
            static::assertInstanceOf(\DOMElement::class, $resultCell);

            static::assertSame(
                '100.00%',
                $resultCell->textContent,
                "$baseName is merged into the final report",
            );
        }
    }

    #[Test]
    public function testMergeFail(): void
    {
        $fileContent = '<?php return new \stdClass();';
        $fileName = tempnam(sys_get_temp_dir(), 'coverage-merger-3.x-');
        file_put_contents($fileName, $fileContent);
        $phpFiles = new \ArrayIterator([$fileName]);
        $merger = new CoverageMerger();

        static::expectException(\RuntimeException::class);
        $merger->merge($phpFiles);
    }

    protected function runTestWithCodeCoverage(): void
    {
        $projectRootDir = $this->getProjectRootDir();
        $fixturesDir = $this->getFixturesDir();
        $case01Dir = "$fixturesDir/case-01";
        $phpunitExecutable = "$projectRootDir/vendor/bin/phpunit";

        $cmdPattern = 'cd %s ; %s --coverage-php=%s --coverage-xml=%s --coverage-clover=%s --coverage-html=%s %s 2>&1';
        $cmdArgs = [
            'cwd' => escapeshellarg($case01Dir),
            'phpunitExecutable' => escapeshellcmd($phpunitExecutable),
        ];

        $files = [
            'A.php',
            'B.php',
            'C.php',
        ];

        if (!file_exists("$case01Dir/reports")) {
            mkdir("$case01Dir/reports", 0777 - umask(), true);
        }
        foreach ($files as $codeFile) {
            $dstPhp = $codeFile;
            $dstXml = preg_replace('/\.php$/', '.xml', $codeFile);
            $dstClover = preg_replace('/\.php$/', '.clover.xml', $codeFile);
            $dstHtml = preg_replace('/\.php$/', '.html', $codeFile);
            $testFile = preg_replace('/\.php$/', 'Test.php', $codeFile);
            $cmdArgs['coverage-php'] = escapeshellarg("$case01Dir/reports/$dstPhp");
            $cmdArgs['coverage-xml'] = escapeshellarg("$case01Dir/reports/$dstXml");
            $cmdArgs['coverage-clover'] = escapeshellarg("$case01Dir/reports/$dstClover");
            $cmdArgs['coverage-html'] = escapeshellarg("$case01Dir/reports/$dstHtml");
            $cmdArgs['testFile'] = escapeshellarg("$case01Dir/tests/$testFile");
            $command = vsprintf(
                $cmdPattern,
                $cmdArgs,
            );

            $output = [];
            exec($command, $output, $exitCode);
            if ($exitCode !== 0) {
                throw new \Exception(implode("\n", $output));
            }
        }
    }

    protected function getProjectRootDir(): string
    {
        return dirname(__DIR__, 3);
    }

    protected function getFixturesDir(): string
    {
        return $this->getProjectRootDir() . '/tests/fixtures';
    }
}
