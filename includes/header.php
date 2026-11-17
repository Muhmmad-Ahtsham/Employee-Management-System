<?php
// Expects: $pageTitle (string). functions.php must already be included.
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'Employee Management') ?> | Employee Management</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="app-header">
    <div class="bar">
      <a class="brand" href="index.php">Employee Management System</a>
      <nav aria-label="Main navigation">
        <?php if (is_logged_in()): ?>
          <a href="index.php">Employees</a>
          <a href="create.php">Add Employee</a>
          <span class="user">Signed in as <strong><?= e($_SESSION['user']) ?></strong></span>
          <a class="logout" href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <main class="container">
    <?php if ($flash): ?>
      <div class="alert <?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
    <?php endif; ?>
