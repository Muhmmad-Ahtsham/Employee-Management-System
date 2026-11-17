<?php
declare(strict_types=1);

/* ============================================================
   Lab 9: shared helpers (included at the TOP of every page,
   before any HTML output, so session_start() works)
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const DEPARTMENTS = ['Human Resources', 'Finance', 'IT', 'Marketing', 'Operations', 'Sales'];
const DATA_FILE   = __DIR__ . '/../data/employees.json';

// Demo login gate (Lab 12 replaces this with a database users table).
// Username: admin | Password: admin123  (stored only as a hash)
const USERS = ['admin' => '$2y$10$p8RL22.GG1MyXbFnExjFgODZ7q7jlH0MC5vObtCXZYIadM2tPB6tS'];

/* ---------- Output / navigation helpers ---------- */

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

/* ---------- Flash messages (stored in the session) ---------- */

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/* ---------- Authentication gate ---------- */

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please log in to continue.');
        redirect('login.php');
    }
}

/* ---------- Data storage (JSON file; MySQL arrives in Labs 10-11) ---------- */

function load_employees(): array
{
    if (!is_readable(DATA_FILE)) {
        return [];
    }
    $data = json_decode((string) file_get_contents(DATA_FILE), true);
    return is_array($data) ? $data : [];
}

function save_employees(array $employees): bool
{
    $json = json_encode(array_values($employees), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(DATA_FILE, $json, LOCK_EX) !== false;
}

function find_employee(int $id): ?array
{
    foreach (load_employees() as $employee) {
        if ((int) $employee['id'] === $id) {
            return $employee;
        }
    }
    return null;
}

/* ---------- Reusable server-side validation ---------- */

/**
 * Validates and cleans employee form input.
 * Returns [$cleanData, $errors]; the form is valid when $errors is empty.
 */
function validate_employee(array $input): array
{
    $errors = [];
    $data = [
        'name'        => trim((string) ($input['name'] ?? '')),
        'email'       => strtolower(trim((string) ($input['email'] ?? ''))),
        'department'  => trim((string) ($input['department'] ?? '')),
        'designation' => trim((string) ($input['designation'] ?? '')),
        'salary'      => trim((string) ($input['salary'] ?? '')),
        'joined'      => trim((string) ($input['joined'] ?? '')),
    ];

    if ($data['name'] === '') {
        $errors['name'] = 'Name is required.';
    } elseif (strlen($data['name']) < 3 || strlen($data['name']) > 100) {
        $errors['name'] = 'Name must be between 3 and 100 characters.';
    }

    if ($data['email'] === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } else {
        foreach (load_employees() as $existing) {
            if (strtolower($existing['email']) === $data['email']) {
                $errors['email'] = 'This email is already registered.';
                break;
            }
        }
    }

    if (!in_array($data['department'], DEPARTMENTS, true)) {
        $errors['department'] = 'Select a valid department.';
    }

    if ($data['designation'] === '') {
        $errors['designation'] = 'Designation is required.';
    } elseif (strlen($data['designation']) > 80) {
        $errors['designation'] = 'Designation must be 80 characters or fewer.';
    }

    if ($data['salary'] === '') {
        $errors['salary'] = 'Salary is required.';
    } elseif (!is_numeric($data['salary']) || (float) $data['salary'] <= 0) {
        $errors['salary'] = 'Salary must be a number greater than zero.';
    }

    $date = DateTime::createFromFormat('Y-m-d', $data['joined']);
    if ($data['joined'] === '') {
        $errors['joined'] = 'Joining date is required.';
    } elseif (!$date || $date->format('Y-m-d') !== $data['joined']) {
        $errors['joined'] = 'Enter a valid date.';
    } elseif ($date > new DateTime('today')) {
        $errors['joined'] = 'Joining date cannot be in the future.';
    }

    return [$data, $errors];
}
