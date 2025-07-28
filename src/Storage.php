<?php

class Storage
{
    private string $filePath;

    public function __construct()
    {
        // 1. set path to tasks.json
        $this->filePath = __DIR__ . '/../tasks.json';

        // 2. if file doesn't exist create it with empty array
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([]));
        }
    }

    public function load(): array
    {
        // 1. read file contents
        $json = file_get_contents($this->filePath);

        // 2. deconde json to php array
        $data = json_decode($json, true);

        // 3. if decode fails or not an array return empty array
        if (!is_array($data)) {
            return [];
        }

        // 4. return the decoded array
        return $data;
    }

    public function save(array $tasks): void
    {
        // 1. convert array to json
        $json = json_encode($tasks, JSON_PRETTY_PRINT);

        // 2. write json to file
        file_put_contents($this->filePath, $json);
    }
}