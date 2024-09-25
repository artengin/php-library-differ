<?php

namespace Differ\Formatter;

use Differ\Formatters\Stylish;
use Differ\Formatters\Json;
use Differ\Formatters\Plain;

function format(array $diff, string $format): string
{
    return match ($format) {
        'stylish' => Stylish\render($diff),
        'json' => Json\render($diff),
        'plain' => Plain\render($diff),
        default => throw new \Exception(sprintf('Unknown data format: "%s"!', $format)),
    };
}
