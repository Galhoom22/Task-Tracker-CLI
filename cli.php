<?php

// 1. read command line arguments
$args = $argv;

// 2. remove the first element (script name)
array_shift($args);

// 3. check if user provided at least one command
if (count($args) === 0) {
    echo "❌ Error: You must provide a command like (add, update, delete, mark-done, mark-in-progress, list, list done, list todo, list in-progress)\n";
    exit(1);
}

// 4. extract the command name like (add, update)
$command = $args[0];

// 5. save the path
$commandFile = __DIR__ . "/commands/{$command}.php";

// 6. check if command file exists
if (!file_exists($commandFile)) {
    echo "❌ Error: unknown command '{$command}'\n";
    exit(1);
}

// 7. load the command file
require $commandFile;
