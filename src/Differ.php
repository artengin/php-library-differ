<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Formatter\format;
use function Differ\Parser\parser;

const UNCHANGED = 'unchanged';
const CHANGED = 'changed';
const ADDED = 'added';
const DELETED = 'deleted';
const NESTED = 'nested';

function genDiff(string $file1, string $file2, string $format = 'stylish'): string
{
    ['extension' => $extension1, 'content' => $contentFile1] = getContents($file1);
    ['extension' => $extension2, 'content' => $contentFile2]  = getContents($file2);

    $valueFile1 = parser($extension1, $contentFile1);
    $valueFile2 = parser($extension2, $contentFile2);

    $valueDiff = buildDiff($valueFile1, $valueFile2);
    $valueDiffWithRoot = addingRootNode($valueDiff);

    return format($valueDiffWithRoot, $format);
}
function getContents(string $path): array
{
    if (!file_exists($path)) {
        throw new \Exception("Invalid file path: {$path}");
    }

    return [
        'extension' => pathinfo($path, PATHINFO_EXTENSION),
        'content' => file_get_contents($path),
    ];
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
                'key' => $key,
                'type' => NESTED,
                'children' => buildDiff($valueFirst, $valueSecond),
            ];
        }

        if (!array_key_exists($key, $first)) {
            return [
                'key' => $key,
                'type' => ADDED,
                'value' => $valueSecond,
            ];
        }

        if (!array_key_exists($key, $second)) {
            return [
                'key' => $key,
                'type' => DELETED,
                'value' => $valueFirst,
            ];
        }

        if ($valueFirst === $valueSecond) {
            return [
                'key' => $key,
                'type' => UNCHANGED,
                'value' => $valueFirst,
            ];
        }

        return [
            'key' => $key,
            'type' => CHANGED,
            'value1' => $valueFirst,
            'value2' => $valueSecond,
        ];
    }, $sortedArray);
}

function addingRootNode(array $value): array
{
    return ['type' => 'root', 'children' => $value];
}
