<?php

declare(strict_types=1);

define('DATA_FILE', __DIR__ . '/data/tasks.json');

require_once __DIR__ . '/src/storage.php';
require_once __DIR__ . '/src/cli_handler.php';
require_once __DIR__ . '/src/validator.php';
require_once __DIR__ . '/src/output.php';
require_once __DIR__ . '/src/task_manager.php';
require_once __DIR__ . '/src/dispatcher.php';

$parsed     = parse_input($argv);
$validation = validate_command($parsed);

if (!$validation['valid']) {
    output_error($validation['error']);
    exit(1);
}

dispatch($parsed, DATA_FILE);
