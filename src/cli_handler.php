<?php

declare(strict_types=1);

function parse_input(array $argv): array
{
    return [
        'command'   => $argv[1] ?? 'help',
        'arguments' => array_slice($argv, 2),
    ];
}
