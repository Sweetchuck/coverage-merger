<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Tests\Fixtures\Case01\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sweetchuck\CoverageMerger\Tests\Fixtures\Case01\B;

#[CoversClass(B::class)]
class BTest extends TestCase
{
    #[Test]
    public function testCreate(): void
    {
        $b = new B();
        $this->assertSame('pong', $b->ping());
    }
}
