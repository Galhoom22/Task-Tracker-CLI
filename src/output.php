<?php

declare(strict_types=1);

function output_success(string $message): void
{
    echo "[OK] {$message}" . PHP_EOL;
}

function output_error(string $message): void
{
    fwrite(STDERR, "[ERROR] {$message}" . PHP_EOL);
}

function output_task_list(array $tasks): void
{
    if (empty($tasks)) {
        echo 'No tasks found.' . PHP_EOL;
        return;
    }

    foreach ($tasks as $task) {
        echo "#{$task['id']} | {$task['status']} | {$task['description']} | {$task['createdAt']}" . PHP_EOL;
    }
}

function output_help(): void
{
    echo "Usage:" . PHP_EOL;
    echo "  php task-cli.php add \"<description>\"" . PHP_EOL;
    echo "  php task-cli.php update <id> \"<description>\"" . PHP_EOL;
    echo "  php task-cli.php delete <id>" . PHP_EOL;
    echo "  php task-cli.php mark-in-progress <id>" . PHP_EOL;
    echo "  php task-cli.php mark-done <id>" . PHP_EOL;
    echo "  php task-cli.php list [todo|in-progress|done]" . PHP_EOL;
}
