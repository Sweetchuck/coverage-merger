<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Test\Fixtures\Case01\Tests;

use PHPUnit\Framework\TestCase;
use Sweetchuck\CoverageMerger\Test\Fixtures\Case01\B;

/**
 * @covers \Sweetchuck\CoverageMerger\Test\Fixtures\Case01\B
 */
class BTest extends TestCase
{
    public function testCreate()
    {
        $b = new B();
        $this->assertSame('pong', $b->ping());
    }
}
