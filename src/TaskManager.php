<?php

require_once __DIR__ . '/Storage.php'; // to deal with (JSON file)

class TaskManager
{
    private $storage;

    public function __construct()
    {
        // Create a new instance of the Storage class
        $this->storage = new Storage();
    }

    public function addTask(string $description): array
    {
        // 1. Load all existing tasks from storage
        $tasks = $this->storage->load();

        // 2. Generate a new unique ID for the task
        $id = $this->generateNextId($tasks);

        // 3. Get current date and time 
        $now = date(DATE_RFC3339);

        // 4. Create the new task as an associative array
        $task = [
            'id' => $id,
            'description' => $description,
            'status' => 'todo',
            'createdAt' => $now,
            'updatedAt' => $now
        ];

        // 5. Add the new task to the list
        $tasks[] = $task;

        // 6. Save the updated task list back to the file
        $this->storage->save($tasks);

        // 7. Return the new task so CLI can use it
        return $task;
    }

    private function generateNextId(array $tasks): int
    {
        // 1. Extract all IDs and find the max
        $maxId = 0;

        foreach ($tasks as $task) {
            if ($task['id'] > $maxId) {
                $maxId = $task['id'];
            }
        }

        // 2. Return max ID + 1
        return $maxId + 1;
    }
}