# Task Tracker CLI

A simple command-line tool to track and manage your tasks, built in pure PHP 8.x with no external dependencies.

---

## Requirements

- PHP 8.0 or higher
- No external libraries or frameworks required

---

## Installation

Clone the repository and navigate to the project directory:

```bash
git clone https://github.com/your-username/task-tracker-cli.git
cd task-tracker-cli
```

No installation or build step needed.

---

## Usage

All commands are run from the project root using `php task-cli.php`.

### Add a task

```bash
php task-cli.php add "Buy groceries"
# Output: [OK] Task added successfully (ID: 1).
```

### Update a task

```bash
php task-cli.php update 1 "Buy groceries and cook dinner"
# Output: [OK] Task 1 updated.
```

### Delete a task

```bash
php task-cli.php delete 1
# Output: [OK] Task 1 deleted.
```

### Mark a task as in-progress

```bash
php task-cli.php mark-in-progress 1
# Output: [OK] Task 1 marked as in-progress.
```

### Mark a task as done

```bash
php task-cli.php mark-done 1
# Output: [OK] Task 1 marked as done.
```

### List all tasks

```bash
php task-cli.php list
```

### List tasks by status

```bash
php task-cli.php list todo
php task-cli.php list in-progress
php task-cli.php list done
```

### Show help

```bash
php task-cli.php
```

---

## Task Properties

Each task stored in `tasks.json` has the following fields:

| Field | Description |
|---|---|
| `id` | Unique auto-incrementing integer |
| `description` | Text description of the task |
| `status` | One of: `todo`, `in-progress`, `done` |
| `createdAt` | Timestamp when the task was created |
| `updatedAt` | Timestamp of the last update |

Example `tasks.json`:

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

```
task-tracker/
│
├── task-cli.php          # Entry point
│
├── src/
│   ├── cli_handler.php   # Parses $argv into a command array
│   ├── dispatcher.php    # Routes commands to task manager functions
│   ├── task_manager.php  # All task CRUD logic
│   ├── storage.php       # JSON file read/write
│   ├── validator.php     # Input validation per command
│   └── output.php        # All terminal output
│
├── tasks.json            # Auto-created on first run
└── README.md
```

---

## Error Handling

All errors are printed to `STDERR` and the process exits with code `1`. Examples:

```bash
php task-cli.php add            # Missing description
php task-cli.php add "   "      # Whitespace-only description
php task-cli.php delete 999     # Non-existent ID
php task-cli.php list unknown   # Invalid status filter
php task-cli.php bad-command    # Unknown command
```
