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
    $result = iter($data['children']);
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

        $compare = $val['type'];
        $delete = COMPARE_TEXT_SYMBOL_MAP[DELETED];
        $add = COMPARE_TEXT_SYMBOL_MAP[ADDED];
        $compareSymbol = COMPARE_TEXT_SYMBOL_MAP[$compare];
        $key = $val['key'];

        return match ($compare) {
            CHANGED => structure($val['value1'], $key, $delete, $depth) . structure($val['value2'], $key, $add, $depth),
            NESTED => structure(iter($val['children'], $depth + 1), $key, $compareSymbol, $depth),
            default => structure($val['value'], $key, $compareSymbol, $depth),
        };
    };

    $result = array_map($func, $value);
    $closeBracketIndentSize = $depth * SPACECOUNT;
    $closeBracketIndent = $closeBracketIndentSize > 0 ? str_repeat(REPLACER, $closeBracketIndentSize - SPACECOUNT) : '';

    return "{\n" . implode($result) . "{$closeBracketIndent}}";
}

function structure(mixed $value, string $key, string $compareSymbol, int $depth): string
{
    $indentSize = ($depth * SPACECOUNT) - 2;
    $currentIndent = str_repeat(REPLACER, $indentSize);
    $depthNested = $depth + 1;
    $valueStructured = depthStructuring($value, $depthNested);

    $result = sprintf(
        "%s%s %s: %s\n",
        $currentIndent,
        $compareSymbol,
        $key,
        $valueStructured,
    );
    return $result;
}
function depthStructuring(mixed $value, int $depth): string
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
            depthStructuring($val, $depth + 1),
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
