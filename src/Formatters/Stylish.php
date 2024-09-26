<?php

namespace Differ\Formatters\Stylish;

use const Differ\Differ\ADDED;
use const Differ\Differ\DELETED;
use const Differ\Differ\CHANGED;
use const Differ\Differ\NESTED;
use const Differ\Differ\UNCHANGED;

const SPACECOUNT = 4;
const REPLACER = ' ';
const COMPARE_TEXT_SYMBOL_MAP = [
    ADDED => '+',
    DELETED => '-',
    CHANGED => ' ',
    NESTED => ' ',
    UNCHANGED => ' ',
];

function render(array $data): string
{
    $result = iter($data);
    return $result;
}

function iter(array $value, int $depth = 1): string
{
    $func = function ($val) use ($depth) {
        if (!is_array($val)) {
            return toString($val);
        }

        if (!array_key_exists(0, $val) && !array_key_exists('type', $val)) {
            return toString($val);
        }

        $indentSize = ($depth * SPACECOUNT) - 2;
        $currentIndent = str_repeat(REPLACER, $indentSize);
        $compare = $val['type'];
        $key = $val['key'];
        $depthNested = $depth + 1;
        $compareSymbol = COMPARE_TEXT_SYMBOL_MAP[$compare];

        if ($compare === CHANGED) {
            $valChanged1 = stringify($val['value1'], $depthNested);
            $valChanged2 = stringify($val['value2'], $depthNested);
            $resultChanged1 = sprintf(
                "%s%s %s: %s\n",
                $currentIndent,
                COMPARE_TEXT_SYMBOL_MAP[DELETED],
                $key,
                $valChanged1,
            );
            $resultChanged2 = sprintf(
                "%s%s %s: %s\n",
                $currentIndent,
                COMPARE_TEXT_SYMBOL_MAP[ADDED],
                $key,
                $valChanged2,
            );
            return $resultChanged1 . $resultChanged2;
        } elseif ($compare === NESTED) {
            $valNested = iter($val['children'], $depthNested);
            $resultNested = sprintf(
                "%s%s %s: %s\n",
                $currentIndent,
                $compareSymbol,
                $key,
                $valNested,
            );
            return $resultNested;
        } else {
            $valUnchanged = stringify($val['value'], $depthNested);
            $resultUnchanged = sprintf(
                "%s%s %s: %s\n",
                $currentIndent,
                $compareSymbol,
                $key,
                $valUnchanged,
            );
            return $resultUnchanged;
        }
    };

    $result = array_map($func, $value);
    $closeBracketIndentSize = $depth * SPACECOUNT;
    $closeBracketIndent = $closeBracketIndentSize > 0 ? str_repeat(REPLACER, $closeBracketIndentSize - SPACECOUNT) : '';

    return "{\n" . implode($result) . "{$closeBracketIndent}}";
}

function stringify(mixed $value, int $depth): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    $indentSize = $depth * SPACECOUNT;
    $currentIndent = str_repeat(REPLACER, $indentSize);

    $fun = function ($key, $val) use ($depth, $currentIndent) {
        return sprintf(
            "%s%s: %s\n",
            $currentIndent,
            $key,
            stringify($val, $depth + 1),
        );
    };

    $result = array_map($fun, array_keys($value), $value);
    $closeBracketIndent = str_repeat(REPLACER, $indentSize - SPACECOUNT);

    return "{\n" . implode($result) . "{$closeBracketIndent}}";
}

function toString(mixed $value): string
{
    return match (true) {
        $value === true => 'true',
        $value === false => 'false',
        is_null($value) => 'null',
        default => trim((string) $value, "'")
    };
}
