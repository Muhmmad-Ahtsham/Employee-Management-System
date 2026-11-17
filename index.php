<?php
require __DIR__ . '/includes/functions.php';
require_login();

// Server-side filtering by department (GET). Only allow-listed values are accepted.
$selected = $_GET['department'] ?? '';
if ($selected !== '' && !in_array($selected, DEPARTMENTS, true)) {
    $selected = '';
}

$all = load_employees();
$employees = $selected === ''
    ? $all
    : array_values(array_filter($all, fn($emp) => $emp['department'] === $selected));

$pageTitle = 'Employees';
require __DIR__ . '/includes/header.php';
?>
<div class="title-row">
  <h1>Employees</h1>
  <a class="btn primary" href="create.php">+ Add Employee</a>
</div>

<form class="filter" method="get" action="index.php">
  <label for="department">Filter by department</label>
  <select id="department" name="department">
    <option value="">All departments</option>
    <?php foreach (DEPARTMENTS as $dept): ?>
      <option value="<?= e($dept) ?>" <?= $dept === $selected ? 'selected' : '' ?>><?= e($dept) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn">Apply</button>
  <?php if ($selected !== ''): ?><a class="btn" href="index.php">Clear</a><?php endif; ?>
</form>

<p class="count">Showing <strong><?= count($employees) ?></strong> of <?= count($all) ?> employees</p>

<div class="table-wrap">
  <table>
    <caption class="sr-only">Employee list</caption>
    <thead>
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Department</th><th>Designation</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php if (!$employees): ?>
        <tr><td colspan="6" class="empty">No employees found.</td></tr>
      <?php endif; ?>
      <?php foreach ($employees as $emp): ?>
        <tr>
          <td><?= (int) $emp['id'] ?></td>
          <td><?= e($emp['name']) ?></td>
          <td><?= e($emp['email']) ?></td>
          <td><?= e($emp['department']) ?></td>
          <td><?= e($emp['designation']) ?></td>
          <td><a class="btn small" href="view.php?id=<?= (int) $emp['id'] ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
