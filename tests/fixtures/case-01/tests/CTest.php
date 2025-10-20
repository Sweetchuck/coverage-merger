<?php

declare(strict_types = 1);

namespace Sweetchuck\CoverageMerger\Tests\Fixtures\Case01\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sweetchuck\CoverageMerger\Tests\Fixtures\Case01\C;

#[CoversClass(C::class)]
class CTest extends TestCase
{
    #[Test]
    public function testCreate(): void
    {
        $c = new c();
        $this->assertSame('pong', $c->ping());
    }
}
