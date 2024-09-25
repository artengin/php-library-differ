<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parser(string $extension, string $content): array
{
    return match ($extension) {
        "json" => json_decode($content, true, 512, JSON_THROW_ON_ERROR),
        "yml", "yaml" => Yaml::parse($content),
        default => throw new \Exception("Format {$extension} not supported."),
    };
}
