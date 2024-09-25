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

function render(array $data, int $depth = 1): string
{
    $fun = function ($value) use ($depth) {
        $indentSize = ($depth * SPACECOUNT) - 2;
        $currentIndent = str_repeat(REPLACER, $indentSize);

        $compare = $value['type'];
        $key = $value['key'];
        $compareSymbol = COMPARE_TEXT_SYMBOL_MAP[$compare];

        if ($compare === CHANGED) {
            $val1 = stringify($value['value1'], $depth + 1);
            $val2 = stringify($value['value2'], $depth + 1);
            $result1 = sprintf(
                "%s%s %s: %s\n",
                $currentIndent,
                COMPARE_TEXT_SYMBOL_MAP[DELETED],
                $key,
                $val1,
            );
            $result2 = sprintf(
                "%s%s %s: %s\n",
                $currentIndent,
                COMPARE_TEXT_SYMBOL_MAP[ADDED],
                $key,
                $val2,
            );
            return $result1 . $result2;
        }

        if ($compare === NESTED) {
            $val = render($value['children'], $depth + 1);
        } else {
            $val = stringify($value['value'], $depth + 1);
        }
        $result3 = sprintf(
            "%s%s %s: %s\n",
            $currentIndent,
            $compareSymbol,
            $key,
            $val,
        );
        return $result3;
    };
    $result = array_map($fun, $data);

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
