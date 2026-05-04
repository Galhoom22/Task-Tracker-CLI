# Task Tracker CLI — Project Blueprint

## Project Overview

A command-line tool built in **pure PHP 8.5 (procedural)** that allows users to manage tasks through positional arguments. Tasks are persisted in a local JSON file. No external libraries or frameworks are used.

---

## Technical Constraints & Requirements

| Constraint | Detail |
|---|---|
| Language | PHP 8.5 — latest syntax and features only |
| Paradigm | Procedural — strictly no OOP |
| Dependencies | None — pure native PHP only |
| Reference | https://www.php.net/manual/en/ |
| Persistence | Native filesystem functions + local JSON file |
| Input | CLI (`$argv`, `$argc`) |

### Software Engineering Principles to Apply
- **SoC** — Separation of Concerns: each file has one responsibility
- **DRY** — Don't Repeat Yourself: extract repeated logic into functions
- **KISS** — Keep It Simple, Stupid: no over-engineering
- **YAGNI** — You Aren't Gonna Need It: build only what is required

---

## Project Structure

```
task-tracker/
│
├── task-cli.php              # Entry point — bootstraps the app, requires files, calls dispatch
│
├── src/
│   ├── cli_handler.php       # Parses $argv/$argc into a structured command array
│   ├── dispatcher.php        # Routes commands to the correct handler function
│   ├── task_manager.php      # All task CRUD logic (pure array manipulation)
│   ├── storage.php           # JSON file read/write (filesystem I/O only)
│   ├── validator.php         # Input validation rules per command
│   └── output.php            # Terminal output formatting (tables, messages, errors)
│
├── data/
│   └── tasks.json            # Auto-created on first run; never manually edited
│
└── README.md
```

### Data Flow (strictly one-directional)

```
task-cli.php → cli_handler → validator → dispatcher → task_manager ↔ storage
                                                             ↓
                                                         output.php
```

> No layer should reach "backwards" up the chain. The entry point (`task-cli.php`) must stay under ~15 lines of logic — it connects components, it does not do work.

---

## Task Data Shape

Each task stored in `tasks.json` should have the following fields:

```json
{
  "meta": {
    "next_id": 4
  },
  "tasks": [
    {
      "id": 1,
      "description": "Buy groceries",
      "status": "todo",
      "created_at": "2025-01-01 10:00:00",
      "updated_at": "2025-01-01 10:00:00"
    }
  ]
}
```

### Allowed Status Values
- `todo` — default status on creation
- `in-progress`
- `done`

---

## CLI Usage Reference

```bash
# Add a task
php task-cli.php add "Buy groceries"

# Update a task description
php task-cli.php update 1 "Buy groceries and cook dinner"

# Delete a task
php task-cli.php delete 1

# Mark task status
php task-cli.php mark-in-progress 1
php task-cli.php mark-done 1

# List tasks
php task-cli.php list
php task-cli.php list todo
php task-cli.php list in-progress
php task-cli.php list done
```

---

## Numbered Features — Build In This Order

### Feature 1 — Storage Layer (`src/storage.php`) ✅

**Goal:** Build the foundation that reads and writes the JSON file. This is the most isolated piece — build it first so everything else has a stable base.

**Functions to implement:**
- `load_tasks(string $filepath): array` — reads the JSON file and returns a PHP array. If the file does not exist, returns the default empty structure (with `meta.next_id = 1` and an empty `tasks` array).
- `save_tasks(string $filepath, array $data): void` — writes the PHP array to the JSON file using the atomic write pattern (write to a temp file first, then rename).

**Key behaviors:**
- Auto-create `data/tasks.json` on first run if it doesn't exist
- Use `JSON_PRETTY_PRINT` when encoding so the file is human-readable
- Use `tempnam()` + `rename()` for atomic writes to prevent file corruption
- Always check the return value of file operations — never assume they succeed

**PHP functions to study:** `file_get_contents()`, `file_put_contents()`, `json_encode()`, `json_decode()`, `file_exists()`, `is_writable()`, `tempnam()`, `rename()`

---

### Feature 2 — CLI Input Handler (`src/cli_handler.php`) ✅

**Goal:** Parse the raw `$argv` array into a clean, structured command array that the rest of the app can work with — without caring about filesystem or tasks at all.

**Functions to implement:**
- `parse_input(array $argv): array` — strips the script name from `$argv[0]`, extracts the command name, and collects remaining arguments. Returns a normalized array like:

```php
[
  'command'   => 'add',
  'arguments' => ['Buy groceries'],
]
```

**Key behaviors:**
- Never validate here — only parse and structure
- Handle the case where no command is provided (return a `help` or `none` command)
- Use null coalescing (`??`) for safe array access

**PHP features to use:** `array_slice()`, null coalescing operator `??`

---

### Feature 3 — Validator (`src/validator.php`) ✅

**Goal:** Validate parsed input before any logic runs. Be the gatekeeper.

**Functions to implement:**
- `validate_command(array $parsed_input): array` — returns `['valid' => true]` or `['valid' => false, 'error' => 'message']`

**Validation rules per command:**

| Command | Rules |
|---|---|
| `add` | Requires exactly 1 argument (description); must not be empty or whitespace-only |
| `update` | Requires exactly 2 arguments (id, description); id must be a positive integer |
| `delete` | Requires exactly 1 argument (id); id must be a positive integer |
| `mark-in-progress` | Requires exactly 1 argument (id); id must be a positive integer |
| `mark-done` | Requires exactly 1 argument (id); id must be a positive integer |
| `list` | Requires 0 or 1 argument; if provided, must be one of: `todo`, `in-progress`, `done` |
| Unknown command | Always invalid — return a helpful error message |

**PHP features to use:** `is_numeric()`, `ctype_digit()`, `trim()`, `in_array()`, `match` expression

---

### Feature 4 — Output Formatter (`src/output.php`) ✅

**Goal:** Centralize all terminal output. No `echo` should exist outside this file.

**Functions to implement:**
- `output_success(string $message): void` — prints a success message
- `output_error(string $message): void` — prints an error message to `STDERR`
- `output_task_list(array $tasks): void` — prints a formatted table of tasks
- `output_help(): void` — prints usage instructions

**Key behaviors:**
- Error messages go to `STDERR` using `fwrite(STDERR, ...)` so they don't pollute stdout
- Task list should display: ID, Description, Status, Created At, Updated At
- Format timestamps to be human-readable
- Handle the empty list case gracefully ("No tasks found.")

**PHP functions to study:** `fwrite()`, `STDERR`, `printf()`, `str_pad()`

---

### Feature 5 — Task Manager — Add & List (`src/task_manager.php`) ✅

**Goal:** Implement the first two core task operations. Work only with arrays — never touch files directly.

**Functions to implement:**
- `add_task(array &$data, string $description): int` — creates a new task, appends it to the tasks array, increments `meta.next_id`, returns the new task's ID
- `list_tasks(array $data, ?string $status_filter): array` — returns all tasks, or only tasks matching the given status filter

**Key behaviors for `add_task`:**
- Use `meta.next_id` from the data array as the new task's ID (never count the array)
- Set `status` to `'todo'` by default
- Set both `created_at` and `updated_at` to the current timestamp using `date('Y-m-d H:i:s')`
- Increment `meta.next_id` after creating the task
- Pass `$data` by reference (`&$data`) so mutations are reflected in the caller

**PHP features to use:** `date()`, `array_filter()`, `array_values()`, pass-by-reference (`&`)

---

### Feature 6 — Task Manager — Update & Delete (`src/task_manager.php`) ✅

**Goal:** Extend the task manager with mutation operations.

**Functions to implement:**
- `update_task(array &$data, int $id, string $description): bool` — finds the task by ID, updates its description and `updated_at`, returns `true` on success or `false` if not found
- `delete_task(array &$data, int $id): bool` — removes the task by ID, re-indexes the array, returns `true` on success or `false` if not found

**Key behaviors:**
- Search by `id` field, not by array index
- After deletion, use `array_values()` to re-index the array (so it stays a proper JSON array, not an object)
- Always update `updated_at` on any mutation
- Return a boolean so the dispatcher can report success or failure

**PHP functions to study:** `array_map()`, `array_filter()`, `array_values()`, `array_search()`

---

### Feature 7 — Task Manager — Mark Status (`src/task_manager.php`) ✅

**Goal:** Implement status transitions.

**Functions to implement:**
- `change_task_status(array &$data, int $id, string $new_status): bool` — finds the task by ID, updates its status and `updated_at`, returns `true` or `false`

**Key behaviors:**
- Reuse the same find-by-ID pattern from Feature 6 — identify if it can be extracted into a private helper like `find_task_index(array $tasks, int $id): int|false`
- `new_status` will always be a validated value (`in-progress` or `done`) by the time it reaches here — trust the validator
- Update `updated_at` on status change

> **DRY note:** If you notice that find-by-ID logic is repeated across update, delete, and mark — extract it into a shared helper function inside `task_manager.php`.

---

### Feature 8 — Dispatcher (`src/dispatcher.php`) ✅

**Goal:** Wire commands to task manager functions. This is the traffic controller.

**Functions to implement:**
- `dispatch(array $parsed_input, string $storage_path): void` — loads task data, routes to the correct task manager function based on `$parsed_input['command']`, saves data if mutated, and calls output functions

**Structure guidance:**
- Use a `match` expression on the command name
- Each branch should: call the appropriate `task_manager` function → check the return value → call the appropriate `output_*` function
- Only call `save_tasks()` after a mutating operation (add, update, delete, mark) — not after list
- Pass the storage path in as a parameter (not hardcoded inside)

**PHP features to use:** `match` expression, named arguments (optional, for clarity)

---

### Feature 9 — Entry Point (`task-cli.php`) ✅

**Goal:** The bootstrap file. Thin, clean, and under ~15 lines of logic.

**Responsibilities:**
1. Define the storage path constant (`DATA_FILE`)
2. `require_once` all files from `src/`
3. Call `parse_input($argv)`
4. Call `validate_command($parsed_input)` — if invalid, call `output_error()` and `exit(1)`
5. Call `dispatch($parsed_input, DATA_FILE)`

**Key behaviors:**
- Use `define()` for the JSON file path so it's a true constant
- Exit with code `1` on validation failure (standard CLI convention)
- No business logic here whatsoever

---

### Feature 10 — End-to-End Testing & Edge Cases

**Goal:** Manually test every command and every error path before considering the project complete.

**Test checklist:**

```bash
# Happy path
php task-cli.php add "First task"
php task-cli.php add "Second task"
php task-cli.php list
php task-cli.php update 1 "Updated first task"
php task-cli.php mark-in-progress 1
php task-cli.php mark-done 2
php task-cli.php list done
php task-cli.php delete 1
php task-cli.php list

# Edge cases
php task-cli.php                         # No command
php task-cli.php add                     # Missing description
php task-cli.php add "   "              # Whitespace-only description
php task-cli.php delete 999             # Non-existent ID
php task-cli.php list unknown-status    # Invalid status filter
php task-cli.php unknown-command        # Unknown command
php task-cli.php update 1              # Missing new description
```

**Verify the JSON file** after each mutating operation to confirm data integrity. Verify `meta.next_id` increments correctly and is never reused after a deletion.

---

## Stretch Goals (After Core Is Complete)

### Stretch Goal A — Due Dates & Overdue Detection
Add an optional `--due` flag to the `add` command (e.g., `php task-cli.php add "Buy groceries" --due 2025-02-01`). When listing tasks, flag overdue items visually. Requires date parsing, optional argument handling in the CLI handler, and conditional output formatting.

### Stretch Goal B — Undo Last Action
Implement `php task-cli.php undo` that reverts the most recent mutating operation. Strategy: before every write, snapshot the previous state of the JSON file. This is a single-level backup system and introduces you to state management thinking without a database.

---

## Key Challenges & How to Think Through Them

### Challenge 1 — Generating Collision-Free IDs
Never derive the next ID from `count($tasks)` — it breaks after deletion. Use `meta.next_id` in the JSON file as a monotonically increasing counter. It only ever goes up, never gets reused.

### Challenge 2 — Keeping the Entry Point Thin
If you feel the urge to write logic in `task-cli.php`, that's a signal to create or use a function in `src/`. The entry point should read like a table of contents, not a chapter.

### Challenge 3 — Atomic File Writes
Write to a temp file first with `tempnam()`, then use `rename()` to replace the real file. On most systems, rename within the same filesystem is atomic — it either fully succeeds or doesn't happen, preventing corruption.

---

## PHP Concepts This Project Reinforces

1. **Array manipulation at depth** — `array_map`, `array_filter`, `array_values`, `usort`, `array_column`
2. **Filesystem I/O and error handling without exceptions** — treating `false` returns seriously, always verifying operations succeeded
3. **Modern PHP 8.x syntax** — `match` expressions, null coalescing (`??`), named arguments, union types in function signatures
