<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Blog;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{


    public static function readTimeProvider (): array
    {
        return [ // Provider must return an array
            '1 min reading' => [1],
            // '1 min reading' => ["sfdg"],
            '2 min reading' => [2],
            '3 min reading' => [3],
            '4 min reading' => [4],
        ];

    }

    #[DataProvider('readTimeProvider')]
    public function testReadTimeValid (int $readTime): void
    {
        $blog = new Blog();
        $blog->setReadTime($readTime);

        self::assertSame($readTime, $blog->getReadTime());
    }

}
