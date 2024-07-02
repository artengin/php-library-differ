#!/usr/bin/env php
<?php

namespace Differ\Differ;

function genDiff(string $file1, string $file2): string
{
    $contentFirstFile = parseContent($file1);
    $contentSecondFile = parseContent($file2);

    $contentDiff = findDiff($contentFirstFile, $contentSecondFile);
    return $contentDiff;
}

function parseContent(string $path): array
{
    if (!file_exists($path)) {
        throw new \Exception("Invalid file path: {$path}");
    }

    $content = file_get_contents($path);
    $decodeContent = json_decode($content, true);
    return $decodeContent;
}

function findDiff(array $first, array $second): string
{
    $mergeArray = array_merge($first, $second);
    ksort($mergeArray);

    $resultDiff = array_reduce(array_keys($mergeArray), function ($acc, $key) use ($first, $second) {
        $checkFirst = array_key_exists($key, $first);
        $checkSecond = array_key_exists($key, $second);
        if ($checkFirst && $checkSecond) {
            if ($first[$key] === $second[$key]) {
                $firstValue = isBool($first[$key]);
                $acc[] = "  {$key}: {$firstValue}";
                return $acc;
            }
            $firstValue = isBool($first[$key]);
            $secondValue = isBool($second[$key]);
            $acc[] = "- {$key}: {$firstValue}";
            $acc[] = "+ {$key}: {$secondValue}";
            return $acc;
        }
        if ($checkFirst) {
            $firstValue = isBool($first[$key]);
            $acc[] = "- {$key}: {$firstValue}";
            return $acc;
        }
        $secondValue = isBool($second[$key]);
        $acc[] = "+ {$key}: {$secondValue}";
        return $acc;
    }, []);
    return implode("\n", $resultDiff) . "\n";
}
function isBool($string)
{
    if ($string === false) {
        return 'false';
    }
    if ($string === true) {
        return 'true';
    }
    return $string;
}
