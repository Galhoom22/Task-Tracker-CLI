<?php

declare(strict_types=1);

function add_task(array &$data, string $description): int
{
    $id  = $data['meta']['next_id'];
    $now = date('Y-m-d H:i:s');

    $data['tasks'][] = [
        'id'          => $id,
        'description' => $description,
        'status'      => 'todo',
        'createdAt'   => $now,
        'updatedAt'   => $now,
    ];

    $data['meta']['next_id']++;

    return $id;
}

function list_tasks(array $data, ?string $status_filter): array
{
    if ($status_filter === null) {
        return $data['tasks'];
    }

    $result = [];
    foreach ($data['tasks'] as $task) {
        if ($task['status'] === $status_filter) {
            $result[] = $task;
        }
    }
    return $result;
}

function update_task(array &$data, int $id, string $description): bool
{
    foreach ($data['tasks'] as &$task) {
        if ($task['id'] === $id) {
            $task['description'] = $description;
            $task['updatedAt']   = date('Y-m-d H:i:s');
            return true;
        }
    }
    return false;
}

function delete_task(array &$data, int $id): bool
{
    foreach ($data['tasks'] as $index => $task) {
        if ($task['id'] === $id) {
            array_splice($data['tasks'], $index, 1);
            return true;
        }
    }
    return false;
}

function change_task_status(array &$data, int $id, string $new_status): bool
{
    foreach ($data['tasks'] as &$task) {
        if ($task['id'] === $id) {
            $task['status']     = $new_status;
            $task['updatedAt']  = date('Y-m-d H:i:s');
            return true;
        }
    }
    return false;
}
