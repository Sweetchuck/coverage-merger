<?php

$coverage = new \SebastianBergmann\CodeCoverage\CodeCoverage;
$coverage->setData([]);
$coverage->setTests([]);

$filter = $coverage->filter();
$filter->setWhitelistedFiles([
    '{{ baseDir }}/src/b.php' => true,
]);

return $coverage;
