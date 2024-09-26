<?php

namespace Differ\Formatters\Plain;

use const Differ\Differ\ADDED;
use const Differ\Differ\CHANGED;
use const Differ\Differ\DELETED;
use const Differ\Differ\NESTED;
use const Differ\Differ\UNCHANGED;

const COMPARE_TEXT_MAP = [
    ADDED => 'added',
    DELETED => 'removed',
    CHANGED => 'updated',
    UNCHANGED => '',
    NESTED => '[complex value]',
];

function render(array $data): string
{
    $result = iter($data);
    return rtrim(implode($result), " \n");
}

function iter(array $value, array $acc = []): array
{
    $func = function ($val) use ($acc) {
        if (!is_array($val)) {
            return toString($val);
        }

        if (!array_key_exists(0, $val) && !array_key_exists('type', $val)) {
            return toString($val);
        }

        $key = $val['key'];
        $compare = $val['type'];
        $compareText = COMPARE_TEXT_MAP[$compare];
        $accNew = [...$acc, ...[$key]];

        return match ($compare) {
            ADDED => sprintf(
                "Property '%s' was %s with value: %s\n",
                implode('.', $accNew),
                $compareText,
                toString($val['value']),
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
                toString($val['value1']),
                toString($val['value2']),
            ),
            NESTED => implode(iter($val['children'], $accNew)),
            default => null,
        };
    };

    $result = array_map($func, $value);
    return $result;
}

function toString(mixed $value): string
{
    return match (true) {
        $value === true => 'true',
        $value === false => 'false',
        is_null($value) => 'null',
        is_array($value) || is_object($value) => '[complex value]',
        is_string($value) => "'{$value}'",
        default => trim((string) $value, "'")
    };
}
