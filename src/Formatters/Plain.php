<?php

namespace Differ\Formatters\Plain;

use const Differ\Diff\ADDED;
use const Differ\Diff\CHANGED;
use const Differ\Diff\DELETED;
use const Differ\Diff\NESTED;
use const Differ\Diff\UNCHANGED;

const COMPARE_TEXT_MAP = [
    ADDED => 'added',
    DELETED => 'removed',
    CHANGED => 'updated',
    UNCHANGED => '',
    NESTED => '[complex value]',
];

function format(array $data): string
{
    $result = iter($data);
    return rtrim($result, " \n");
}

function iter(mixed $value, array $acc = []): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    if (!array_key_exists(0, $value) && !array_key_exists('compare', $value)) {
        return toString($value);
    }

    $fun = function ($val) use ($acc) {
        $key = $val['key'];
        $compare = $val['compare'];
        $compareText = COMPARE_TEXT_MAP[$compare];
        $accNew = [...$acc, ...[$key]];

        return match ($compare) {
            ADDED => sprintf(
                "Property '%s' was %s with value: %s\n",
                implode('.', $accNew),
                $compareText,
                iter($val['value'], $accNew),
            ),
            DELETED => sprintf(
                "Property '%s' was %s\n",
                implode('.', $accNew),
                $compareText,
            ),
            CHANGED => sprintf(
                "Property '%s' was %s. From %s to %s\n",
                implode('.', $accNew),
                $compareText,
                iter($val['valueFirst'], $accNew),
                iter($val['valueSecond'], $accNew),
            ),
            NESTED => iter($val['value'], $accNew),
            default => null,
        };
    };

    $result = array_map($fun, $value);

    return implode($result);
}

function toString(mixed $value): string
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_array($value)) {
        return '[complex value]';
    }
    if (is_string($value)) {
        return "'{$value}'";
    }
    return trim(var_export($value, true), "'");
}
