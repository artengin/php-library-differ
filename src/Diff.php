<?php

namespace Differ\Differ;

use function Differ\Formatter\formatter;
use function Differ\Parser\parser;

const UNCHANGED = 'unchanged';
const CHANGED = 'changed';
const ADDED = 'added';
const DELETED = 'deleted';
const NESTED = 'nested';

function genDiff(string $file1, string $file2, $format = 'stylish'): string
{
    $valueFile1 = parser($file1);
    $valueFile2 = parser($file2);
    $valueDiff = buildDiff($valueFile1, $valueFile2);
    return formatter($valueDiff, $format);
}

function buildDiff(array $first, array $second): array
{
    $mergeArray = array_merge($first, $second);
    ksort($mergeArray);

    return array_map(function ($key) use ($first, $second) {
        $valueFirst = $first[$key] ?? null;
        $valueSecond = $second[$key] ?? null;

        if (
            (is_array($valueFirst) && !array_is_list($valueFirst)) &&
            (is_array($valueSecond) && !array_is_list($valueSecond))
        ) {
            return [
                'compare' => NESTED,
                'key' => $key,
                'value' => buildDiff($valueFirst, $valueSecond),
            ];
        }

        if (!array_key_exists($key, $first)) {
            return [
                'compare' => ADDED,
                'key' => $key,
                'value' => $valueSecond,
            ];
        }

        if (!array_key_exists($key, $second)) {
            return [
                'compare' => DELETED,
                'key' => $key,
                'value' => $valueFirst,
            ];
        }

        if ($valueFirst === $valueSecond) {
            return [
                'compare' => UNCHANGED,
                'key' => $key,
                'value' => $valueFirst,
            ];
        }

        return [
            'compare' => CHANGED,
            'key' => $key,
            'valueFirst' => $valueFirst,
            'valueSecond' => $valueSecond,
        ];
    }, array_keys($mergeArray));
}
