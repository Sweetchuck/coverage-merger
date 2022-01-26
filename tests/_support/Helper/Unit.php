<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Test\Helper;

use Codeception\Module;

class Unit extends Module
{

    public function runTestWithCodeCoverage()
    {
        $case01Dir = codecept_data_dir('fixtures/case-01');
        $phpunitExecutable = getcwd() . '/vendor/bin/phpunit';

        $cmdPattern = 'cd %s ; %s --coverage-php=%s --coverage-html=%s %s';
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
            $dstHtml = preg_replace('/\.php$/', '.html', $codeFile);
            $testFile = preg_replace('/\.php$/', 'Test.php', $codeFile);
            $cmdArgs['coverage-php'] = escapeshellarg("$case01Dir/reports/$dstPhp");
            $cmdArgs['coverage-html'] = escapeshellarg("$case01Dir/reports/$dstHtml");
            $cmdArgs['testFile'] = escapeshellarg("$case01Dir/tests/$testFile");
            $command = vsprintf(
                $cmdPattern,
                $cmdArgs,
            );

            exec($command, $output, $exitCode);
            if ($exitCode !== 0) {
                throw new \Exception(implode(\PHP_EOL, $output));
            }
        }
    }
}
