<?php

namespace Differ\Formatters\Json;

function render(array $data): string
{
    $jsonData = json_encode($data, JSON_THROW_ON_ERROR);
    return $jsonData;
}
