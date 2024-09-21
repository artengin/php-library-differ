<?php

namespace Differ\Diff;

use function Functional\sort;
use function Differ\Formatter\formatting;
use function Differ\Parser\parser;

const UNCHANGED = 'unchanged';
const CHANGED = 'changed';
const ADDED = 'added';
const DELETED = 'deleted';
const NESTED = 'nested';

function genDiff(string $file1, string $file2, string $format = 'stylish'): string
{
    $contentFile1 = getContents($file1);
    $contentFile2 = getContents($file2);
    $valueFile1 = parser($file1, $contentFile1);
    $valueFile2 = parser($file2, $contentFile2);
    $valueDiff = buildDiff($valueFile1, $valueFile2);
    return formatting($valueDiff, $format);
}
function getContents(string $path): string
{
    if (!file_exists($path)) {
        throw new \Exception("Invalid file path: {$path}");
    }

    $content = file_get_contents($path);
    return $content;
}
function buildDiff(array $first, array $second): array
{
    $uniqueKeys = array_unique(array_merge(array_keys($first), array_keys($second)));
    $sortedArray = sort($uniqueKeys, function ($first, $second) {
        return $first <=> $second;
    });

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
    }, $sortedArray);
}
