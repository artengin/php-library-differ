<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parser(string $path): array
{
    if (!file_exists($path)) {
        throw new \Exception("Invalid file path: {$path}");
    }

    $content = file_get_contents($path);
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    return match ($extension) {
        "json" => jsonFileParse($content),
        "yml", "yaml" => yamlFileParse($content),
        default => throw new \Exception("Format {$extension} not supported."),
    };
}

function jsonFileParse(string $data): array
{
    return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
}

function yamlFileParse(string $data): array
{
    return Yaml::parse($data);
}