<?php

namespace Converter\Phpunit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DiffTest extends TestCase
{
    public function testJson()
    {
        $first = __DIR__ . "/fixtures/file1.json";
        $second = __DIR__ . "/fixtures/file2.json";
        $expected = file_get_contents(__DIR__ . "/fixtures/expected-stylish.txt");
        $this->assertEquals($expected, genDiff($first, $second));
    }
    public function testYaml()
    {
        $first = __DIR__ . "/fixtures/file1.yml";
        $second = __DIR__ . "/fixtures/file2.yml";
        $expected = file_get_contents(__DIR__ . "/fixtures/expected-stylish.txt");
        $this->assertEquals($expected, genDiff($first, $second));
    }
}
