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

    switch ($extension) {
        case "json":
            return json_decode($content, true);
        case "yml":
            return Yaml::parse($content);
        case "yaml":
            return Yaml::parse($content);
        default:
            throw new \Exception("Format {$extension} not supported.");
    }
}
