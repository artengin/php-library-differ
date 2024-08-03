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
        $expectedStylish = file_get_contents(__DIR__ . "/fixtures/expected-stylish.txt");
        $this->assertEquals($expectedStylish, genDiff($first, $second));
        $expectedJson = file_get_contents(__DIR__ . "/fixtures/expected-json.txt");
        $this->assertEquals($expectedJson, genDiff($first, $second, 'json'));
        $expectedPlain = file_get_contents(__DIR__ . "/fixtures/expected-plain.txt");
        $this->assertEquals($expectedPlain, genDiff($first, $second, 'plain'));
    }
    public function testYaml()
    {
        $first = __DIR__ . "/fixtures/file1.yml";
        $second = __DIR__ . "/fixtures/file2.yml";
        $expectedStylish = file_get_contents(__DIR__ . "/fixtures/expected-stylish.txt");
        $this->assertEquals($expectedStylish, genDiff($first, $second));
        $expectedJson = file_get_contents(__DIR__ . "/fixtures/expected-json.txt");
        $this->assertEquals($expectedJson, genDiff($first, $second, 'json'));
        $expectedPlain = file_get_contents(__DIR__ . "/fixtures/expected-plain.txt");
        $this->assertEquals($expectedPlain, genDiff($first, $second, 'plain'));
    }
    public function testMixed()
    {
        $first = __DIR__ . "/fixtures/file1.json";
        $second = __DIR__ . "/fixtures/file2.yml";
        $expectedStylish = file_get_contents(__DIR__ . "/fixtures/expected-stylish.txt");
        $this->assertEquals($expectedStylish, genDiff($first, $second, 'stylish'));
        $expectedJson = file_get_contents(__DIR__ . "/fixtures/expected-json.txt");
        $this->assertEquals($expectedJson, genDiff($first, $second, 'json'));
        $expectedPlain = file_get_contents(__DIR__ . "/fixtures/expected-plain.txt");
        $this->assertEquals($expectedPlain, genDiff($first, $second, 'plain'));
    }
}
