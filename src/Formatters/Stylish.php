<?php

namespace Differ\Formatters\Stylish;

use const Differ\Diff\ADDED;
use const Differ\Diff\DELETED;
use const Differ\Diff\CHANGED;
use const Differ\Diff\NESTED;
use const Differ\Diff\UNCHANGED;

const SPACECOUNT = 4;
const REPLACER = ' ';
const COMPARE_TEXT_SYMBOL_MAP = [
    ADDED => '+',
    DELETED => '-',
    CHANGED => ' ',
    NESTED => ' ',
    UNCHANGED => ' ',
];

function format(array $data, int $depth = 1): string
{
    $fun = function ($value) use ($depth) {
        $indentSize = ($depth * SPACECOUNT) - 2;
        $currentIndent = str_repeat(REPLACER, $indentSize);

        $compare = $value['compare'];
        $key = $value['key'];
        $compareSymbol = COMPARE_TEXT_SYMBOL_MAP[$compare];

        if ($compare === CHANGED) {
            $val1 = stringify($value['valueFirst'], $depth + 1);
            $val2 = stringify($value['valueSecond'], $depth + 1);
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
            $val = format($value['value'], $depth + 1);
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
    if (is_null($value)) {
        return 'null';
    }
    return trim(var_export($value, true), "'");
}
