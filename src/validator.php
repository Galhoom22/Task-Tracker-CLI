<?php

declare(strict_types=1);

function validate_command(array $parsed_input): array
{
    $command = $parsed_input['command'];
    $args = $parsed_input['arguments'];

    return match ($command) {
        'add' => validate_add($args),
        'update' => validate_update($args),
        'delete', 'mark-in-progress', 'mark-done' => validate_id_only($args),
        'list' => validate_list($args),
        'help' => ['valid' => true],
        default => ['valid' => false, 'error' => "Unknown command: '$command'"]
    };
}

function validate_add(array $args): array
{
    if (count($args) !== 1 || trim($args[0]) === '') {
        return ['valid' => false, 'error' => 'The "add" command requires a non-empty description.'];
    }
    return ['valid' => true];
}

function validate_update(array $args): array
{
    if (count($args) !== 2) {
        return ['valid' => false, 'error' => 'The "update" command requires exactly an ID and a description.'];
    }

    if (!ctype_digit((string)$args[0]) || (int)$args[0] <= 0) {
        return ['valid' => false, 'error' => 'The ID must be a positive integer.'];
    }

    if (trim($args[1]) === '') {
        return ['valid' => false, 'error' => 'The description cannot be empty.'];
    }

    return ['valid' => true];
}

function validate_id_only(array $args): array
{
    if (count($args) !== 1 || !ctype_digit((string)$args[0]) || (int)$args[0] <= 0) {
        return ['valid' => false, 'error' => 'This command requires a positive integer ID.'];
    }
    return ['valid' => true];
}

function validate_list(array $args): array
{
    if (count($args) === 0) return ['valid' => true];

    if (count($args) === 1 && in_array($args[0], ['todo', 'in-progress', 'done'])) {
        return ['valid' => true];
    }

    return ['valid' => false, 'error' => 'The "list" command takes an optional status: todo, in-progress, or done.'];
}