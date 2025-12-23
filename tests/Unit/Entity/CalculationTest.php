<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;

class CalculationTest extends TestCase
{

    public function testCheckEqualNumbers (): void
    {

        $number = 42;

        self::assertSame(42, $number, "Number should be equal to 42"); // ===
//        self::assertEquals(42, $number); // ==

    }

    public function testCheckNotEqualNumbers (): void
    {
        $number = 30;

        self::assertSame(42, $number, "Number should be equal to 42");
    }

}
