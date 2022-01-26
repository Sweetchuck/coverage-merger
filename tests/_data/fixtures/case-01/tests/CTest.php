<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Test\Fixtures\Case01\Tests;

use PHPUnit\Framework\TestCase;
use Sweetchuck\CoverageMerger\Test\Fixtures\Case01\C;

/**
 * @covers \Sweetchuck\CoverageMerger\Test\Fixtures\Case01\C
 */
class CTest extends TestCase
{
    public function testCreate()
    {
        $c = new c();
        $this->assertSame('pong', $c->ping());
    }
}
