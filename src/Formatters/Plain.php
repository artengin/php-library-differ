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

function format(array $data): string
{
    return json_encode($data, JSON_THROW_ON_ERROR);
}

function toString(mixed $value): string
{
    if (is_null($value)) {
        return 'null';
    }
    return trim(var_export($value, true), "'");
}
