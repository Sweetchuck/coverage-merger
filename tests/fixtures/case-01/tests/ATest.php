<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Tests\Fixtures\Case01\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sweetchuck\CoverageMerger\Tests\Fixtures\Case01\A;

#[CoversClass(A::class)]
class ATest extends TestCase
{
    #[Test]
    public function testCreate(): void
    {
        $a = new A();
        $this->assertSame('pong', $a->ping());
    }
}
