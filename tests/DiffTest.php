<?php

namespace Converter\Phpunit\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DiffTest extends TestCase
{
    protected string $fixturesPath = __DIR__ . '/fixtures';

    protected function getExpectedPath(string $formatter): string
    {
        return "{$this->fixturesPath}/expected-{$formatter}.txt";
    }

    protected function getFirstFilePath(string $type): string
    {
        return "{$this->fixturesPath}/file1.{$type}";
    }
    protected function getSecondFilePath(string $type): string
    {
        return "{$this->fixturesPath}/file2.{$type}";
    }
    public static function dataProvider(): array
    {
        return [
            'json - json' => [
                'json',
                'json',
            ],
            'json - yml' => [
                'json',
                'yml',
            ],
            'yml - yml' => [
                'yml',
                'yml',
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testDefault(string $firstFileType, string $secondFileType): void
    {
        $formatter = "stylish";
        $first = $this->getFirstFilePath($firstFileType);
        $second = $this->getSecondFilePath($secondFileType);
        $expected = file_get_contents($this->getExpectedPath($formatter));
        $this->assertEquals($expected, genDiff($first, $second));
    }

    #[DataProvider('dataProvider')]
    public function testStylish(string $firstFileType, string $secondFileType): void
    {
        $formatter = "stylish";
        $first = $this->getFirstFilePath($firstFileType);
        $second = $this->getSecondFilePath($secondFileType);
        $expected = file_get_contents($this->getExpectedPath($formatter));
        $this->assertEquals($expected, genDiff($first, $second, $formatter));
    }

    #[DataProvider('dataProvider')]
    public function testJson(string $firstFileType, string $secondFileType): void
    {
        $formatter = "json";
        $first = $this->getFirstFilePath($firstFileType);
        $second = $this->getSecondFilePath($secondFileType);
        $expected = file_get_contents($this->getExpectedPath($formatter));
        $this->assertEquals($expected, genDiff($first, $second, $formatter));
    }

    #[DataProvider('dataProvider')]
    public function testPlain(string $firstFileType, string $secondFileType): void
    {
        $formatter = "plain";
        $first = $this->getFirstFilePath($firstFileType);
        $second = $this->getSecondFilePath($secondFileType);
        $expected = file_get_contents($this->getExpectedPath($formatter));
        $this->assertEquals($expected, genDiff($first, $second, $formatter));
    }
}
