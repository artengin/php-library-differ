<?php

namespace Differ\Formatter;

use Differ\Formatters\Stylish;
use Differ\Formatters\Json;
use Differ\Formatters\Plain;

function formatting(array $diff, string $format): string
{
    return match ($format) {
        'stylish' => Stylish\format($diff),
        'json' => Json\format($diff),
        'plain' => Plain\format($diff),
        default => throw new \Exception(sprintf('Unknown data format: "%s"!', $format)),
    };
}
