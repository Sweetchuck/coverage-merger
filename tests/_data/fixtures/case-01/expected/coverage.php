<?php
$coverage = new SebastianBergmann\CodeCoverage\CodeCoverage;
$coverage->setData(array (
));
$coverage->setTests(array (
));

$filter = $coverage->filter();
$filter->setWhitelistedFiles(array (
  'vfs://root/case-01/src/a.php' => true,
  'vfs://root/case-01/src/b.php' => true,
  'vfs://root/case-01/src/c.php' => true,
));

return $coverage;
