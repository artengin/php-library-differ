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

function format(array $data, int $depth = 0): string
{
    $fun = function ($acc, $value) use ($depth) {
        $depth++;
        $indentSize = ($depth * SPACECOUNT) - 2;
        $currentIndent = str_repeat(REPLACER, $indentSize);

        $compare = $value['compare'];
        $key = $value['key'];
        $compareSymbol = COMPARE_TEXT_SYMBOL_MAP[$compare];

        if ($compare === CHANGED) {
            $val1 = stringify($value['valueFirst'], $depth);
            $val2 = stringify($value['valueSecond'], $depth);
            $acc[] = sprintf(
                "%s%s %s: %s\n",
                $currentIndent,
                COMPARE_TEXT_SYMBOL_MAP[DELETED],
                $key,
                $val1,
            );
            $acc[] = sprintf(
                "%s%s %s: %s\n",
                $currentIndent,
                COMPARE_TEXT_SYMBOL_MAP[ADDED],
                $key,
                $val2,
            );
            return $acc;
        }

        if ($compare === NESTED) {
            $val = format($value['value'], $depth);
        } else {
            $val = stringify($value['value'], $depth);
        }
        $acc[] = sprintf(
            "%s%s %s: %s\n",
            $currentIndent,
            $compareSymbol,
            $key,
            $val,
        );
        return $acc;
    };
    $result = array_reduce($data, $fun, []);

    $closeBracketIndentSize = $depth * SPACECOUNT;
    $closeBracketIndent = $closeBracketIndentSize > 0 ? str_repeat(REPLACER, $closeBracketIndentSize) : '';

    return "{\n" . implode($result) . "{$closeBracketIndent}}";
}


function stringify(mixed $value, int $depth)
{
    if (!is_array($value)) {
        return toString($value);
    }
    $depth++;
    $indentSize = $depth * SPACECOUNT;
    $currentIndent = str_repeat(REPLACER, $indentSize);
    $fun = function ($key, $val) use ($depth, $currentIndent) {
        return sprintf(
            "%s%s: %s\n",
            $currentIndent,
            $key,
            stringify($val, $depth),
        );
    };
    $result = array_map($fun, array_keys($value), $value);

    $closeBracketIndent = str_repeat(REPLACER, $indentSize - SPACECOUNT);
    return "{\n" . implode($result) . "{$closeBracketIndent}}";
}

function toString($value)
{
    if (is_null($value)) {
        return 'null';
    }
    return trim(var_export($value, true), "'");
}
