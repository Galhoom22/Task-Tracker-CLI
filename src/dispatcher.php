<?php

declare(strict_types=1);

function dispatch(array $parsed_input, string $storage_path): void
{
    $command = $parsed_input['command'];
    $args    = $parsed_input['arguments'];
    $data    = load_tasks($storage_path);

    match ($command) {
        'add' => (function () use (&$data, $args, $storage_path) {
            $id = add_task($data, $args[0]);
            save_tasks($storage_path, $data);
            output_success("Task added successfully (ID: {$id}).");
        })(),

        'update' => (function () use (&$data, $args, $storage_path) {
            $ok = update_task($data, (int) $args[0], $args[1]);
            if ($ok) { save_tasks($storage_path, $data); output_success("Task {$args[0]} updated."); }
            else     { output_error("Task {$args[0]} not found."); }
        })(),

        'delete' => (function () use (&$data, $args, $storage_path) {
            $ok = delete_task($data, (int) $args[0]);
            if ($ok) { save_tasks($storage_path, $data); output_success("Task {$args[0]} deleted."); }
            else     { output_error("Task {$args[0]} not found."); }
        })(),

        'mark-in-progress' => (function () use (&$data, $args, $storage_path) {
            $ok = change_task_status($data, (int) $args[0], 'in-progress');
            if ($ok) { save_tasks($storage_path, $data); output_success("Task {$args[0]} marked as in-progress."); }
            else     { output_error("Task {$args[0]} not found."); }
        })(),

        'mark-done' => (function () use (&$data, $args, $storage_path) {
            $ok = change_task_status($data, (int) $args[0], 'done');
            if ($ok) { save_tasks($storage_path, $data); output_success("Task {$args[0]} marked as done."); }
            else     { output_error("Task {$args[0]} not found."); }
        })(),

        'list' => (function () use ($data, $args) {
            $tasks = list_tasks($data, $args[0] ?? null);
            output_task_list($tasks);
        })(),

        'help' => output_help(),

        default => output_error("Unknown command: '{$command}'.")
    };
}
