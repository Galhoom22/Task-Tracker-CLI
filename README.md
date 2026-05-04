<div align="center">

# Task Tracker CLI

**A simple, dependency-free command-line tool to track and manage your tasks.**

Built in pure PHP 8.x — no Composer, no frameworks, no external libraries.

Project page: [https://roadmap.sh/projects/task-tracker](https://roadmap.sh/projects/task-tracker)

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](#)
[![Dependencies](https://img.shields.io/badge/Dependencies-0-brightgreen?style=flat-square)](#)
[![Project](https://img.shields.io/badge/roadmap.sh-Task%20Tracker-blue?style=flat-square)](https://roadmap.sh/projects/task-tracker)

</div>

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Task Properties](#task-properties)
- [Project Structure](#project-structure)
- [Error Handling](#error-handling)

---

## Features

- Add, update, and delete tasks
- Track task status: `todo`, `in-progress`, `done`
- List all tasks or filter by status
- Persistent JSON storage with atomic writes
- Friendly validation and error messages
- Zero external dependencies

---

## Requirements

| Tool | Version |
|------|---------|
| **PHP** | `8.0` or higher |
| **External libraries** | None |

---

## Installation

```bash
git clone https://github.com/Galhoom22/Task-Tracker-CLI.git
cd Task-Tracker-CLI
```

> No build step. No dependencies. Just run it.

---

## Usage

All commands are run from the project root using `php task-cli.php`.

### Add a task

```bash
php task-cli.php add "Buy groceries"
```
```text
[OK] Task added successfully (ID: 1).
```

### Update a task

```bash
php task-cli.php update 1 "Buy groceries and cook dinner"
```
```text
[OK] Task 1 updated.
```

### Delete a task

```bash
php task-cli.php delete 1
```
```text
[OK] Task 1 deleted.
```

### Change task status

```bash
php task-cli.php mark-in-progress 1
php task-cli.php mark-done 1
```

### List tasks

```bash
php task-cli.php list                # all tasks
php task-cli.php list todo           # only todo
php task-cli.php list in-progress    # only in-progress
php task-cli.php list done           # only done
```

### Show help

```bash
php task-cli.php
```

---

### Command Reference

| Command | Arguments | Description |
|---------|-----------|-------------|
| `add` | `"<description>"` | Create a new task |
| `update` | `<id> "<description>"` | Edit a task's description |
| `delete` | `<id>` | Remove a task |
| `mark-in-progress` | `<id>` | Set status to `in-progress` |
| `mark-done` | `<id>` | Set status to `done` |
| `list` | `[todo\|in-progress\|done]` | List tasks (optionally filtered) |

---

## Task Properties

Each task stored in `tasks.json` has the following fields:

| Field | Type | Description |
|-------|------|-------------|
| `id` | `int` | Unique auto-incrementing identifier |
| `description` | `string` | Text description of the task |
| `status` | `enum` | One of `todo`, `in-progress`, `done` |
| `createdAt` | `datetime` | Timestamp when the task was created |
| `updatedAt` | `datetime` | Timestamp of the last update |

### Example `tasks.json`

```json
{
    "meta": {
        "next_id": 2
    },
    "tasks": [
        {
            "id": 1,
            "description": "Buy groceries",
            "status": "todo",
            "createdAt": "2025-01-01 10:00:00",
            "updatedAt": "2025-01-01 10:00:00"
        }
    ]
}
```

---

## Project Structure

```text
task-tracker-cli/
│
├── task-cli.php           # Entry point
│
├── src/
│   ├── cli_handler.php    # Parses $argv into a command array
│   ├── dispatcher.php     # Routes commands to task manager functions
│   ├── task_manager.php   # All task CRUD logic
│   ├── storage.php        # JSON file read/write (atomic)
│   ├── validator.php      # Input validation per command
│   └── output.php         # All terminal output
│
├── tasks.json             # Auto-created on first run
└── README.md
```

---

## Error Handling

All errors are printed to `STDERR` and the process exits with code `1`.

| Example | Result |
|---------|--------|
| `php task-cli.php add` | Missing description |
| `php task-cli.php add "   "` | Whitespace-only description |
| `php task-cli.php delete 999` | Non-existent ID |
| `php task-cli.php list unknown` | Invalid status filter |
| `php task-cli.php bad-command` | Unknown command |

---

<div align="center">

Built as part of the [roadmap.sh Task Tracker project](https://roadmap.sh/projects/task-tracker).

</div>
