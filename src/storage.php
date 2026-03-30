<?php

declare(strict_types=1);

function load_tasks(string $path): array
{
    $data = file_exists($path) ? json_decode(file_get_contents($path), true) : null;

    return is_array($data) ? $data : [
        'meta'  => ['next_id' => 1],
        'tasks' => [],
    ];
}

function save_tasks(string $path, array $data): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $temp = tempnam($dir, 'task_');
    if (!$temp) return;

    if (file_put_contents($temp, json_encode($data, JSON_PRETTY_PRINT)) !== false) {
        rename($temp, $path);
    } else {
        unlink($temp);
    }
}
