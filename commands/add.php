<?php

// 1. use TaskManager Class
require_once __DIR__ . '/../src/TaskManager.php'; 

// 2. read the description
$args = array_slice($argv, 2);

// 3. Check if description was provided
if (count($args) === 0) {
    echo "❌ Error: Please provide a task description.\n";
    exit(1);
}

// 4. Combine all parts of the description into a single string
$description = implode(' ', $args);

// 5. Create an instance of TaskManager
$taskManager = new TaskManager();

// 6. Call the addTask function 
$task = $taskManager->addTask($description);

// 7. Output a success message with the task ID
echo "✅ Task added successfully (ID: {$task['id']})\n";