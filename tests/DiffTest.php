<?php

namespace Converter\Phpunit\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function Differ\Diff\genDiff;

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
    public static function extensionProvider(): array
    {
        return [
            'stylish format, json - json' => [
                'stylish',
                'json',
                'json',
            ],
            'stylish format, yml - json' => [
                'stylish',
                'yml',
                'json',
            ],
            'stylish format, yml - yml' => [
                'stylish',
                'yml',
                'yml',
            ],
            'plain format, json - json' => [
                'plain',
                'json',
                'json',
            ],
            'plain format, json - yml' => [
                'plain',
                'json',
                'yml',
            ],
            'plain format, yml - yml' => [
                'plain',
                'yml',
                'yml',
            ],
            'json format, json - json' => [
                'json',
                'json',
                'json',
            ],
            'json format, json - yml' => [
                'json',
                'json',
                'yml',
            ],
            'json format, yml - yml' => [
                'json',
                'yml',
                'yml',
            ],
        ];
    }
    #[DataProvider('extensionProvider')]
    public function testDiff(string $formatter, string $firstFileType, string $secondFileType): void
    {
        $first = $this->getFirstFilePath($firstFileType);
        $second = $this->getSecondFilePath($secondFileType);
        $expected = file_get_contents($this->getExpectedPath($formatter));
        $this->assertEquals($expected, genDiff($first, $second, $formatter));
    }
}
