<?php

namespace Differ\Differ;

use function Differ\Parser\parser;

function genDiff(string $file1, string $file2): string
{
    $contentFirstFile = parser($file1);
    $contentSecondFile = parser($file2);

    $contentDiff = findDiff($contentFirstFile, $contentSecondFile);
    return $contentDiff;
}

function findDiff(array $first, array $second): string
{
    $mergeArray = array_merge($first, $second);
    ksort($mergeArray);

    $resultDiff = array_reduce(array_keys($mergeArray), function ($acc, $key) use ($first, $second) {
        $checkFirst = array_key_exists($key, $first);
        $checkSecond = array_key_exists($key, $second);
        if ($checkFirst && $checkSecond) {
            $firstValue = isBool($first[$key]);
            $secondValue = isBool($second[$key]);
            if ($first[$key] === $second[$key]) {
                $acc[] = "  {$key}: {$firstValue}";
                return $acc;
            }
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
    $resultDiffString = implode("\n", $resultDiff);
    return $resultDiffString;
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
