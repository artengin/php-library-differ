<?php

namespace Differ\Formatters\Json;

function render(array $data): string
{
    $root = '{"type":"root","children":';
    $jsonData = json_encode($data, JSON_THROW_ON_ERROR);
    return $root . $jsonData . '}';
}
