<?php
require __DIR__ . '/includes/functions.php';
require_login();

$errors = [];
$old = ['name' => '', 'email' => '', 'department' => '', 'designation' => '', 'salary' => '', 'joined' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$old, $errors] = validate_employee($_POST);   // reusable validation function

    if (!$errors) {
        $employees = load_employees();
        $nextId = $employees ? max(array_column($employees, 'id')) + 1 : 1;
        $old['id'] = $nextId;
        $old['salary'] = (float) $old['salary'];
        $employees[] = $old;

        if (save_employees($employees)) {
            flash('success', 'Employee "' . $old['name'] . '" was added successfully.');
            redirect('view.php?id=' . $nextId);     // Post/Redirect/Get: avoids duplicate submits on refresh
        }
        $errors['form'] = 'Could not save the record. Check write permission on the data folder.';
    }
}

$pageTitle = 'Add Employee';
require __DIR__ . '/includes/header.php';
?>
<h1>Add Employee</h1>

<?php if ($errors): ?>
  <div class="alert error" role="alert">
    <strong>Please fix the following:</strong>
    <ul><?php foreach ($errors as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>

<form method="post" action="create.php" class="panel" novalidate>
  <div class="form-grid">
    <div class="field <?= isset($errors['name']) ? 'invalid' : '' ?>">
      <label for="name">Full Name</label>
      <input id="name" name="name" type="text" value="<?= e($old['name']) ?>">
    </div>
    <div class="field <?= isset($errors['email']) ? 'invalid' : '' ?>">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="<?= e($old['email']) ?>">
    </div>
    <div class="field <?= isset($errors['department']) ? 'invalid' : '' ?>">
      <label for="department">Department</label>
      <select id="department" name="department">
        <option value="">Select department</option>
        <?php foreach (DEPARTMENTS as $dept): ?>
          <option value="<?= e($dept) ?>" <?= $old['department'] === $dept ? 'selected' : '' ?>><?= e($dept) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field <?= isset($errors['designation']) ? 'invalid' : '' ?>">
      <label for="designation">Designation</label>
      <input id="designation" name="designation" type="text" value="<?= e($old['designation']) ?>">
    </div>
    <div class="field <?= isset($errors['salary']) ? 'invalid' : '' ?>">
      <label for="salary">Monthly Salary (Rs.)</label>
      <input id="salary" name="salary" type="number" step="0.01" min="0" value="<?= e((string) $old['salary']) ?>">
    </div>
    <div class="field <?= isset($errors['joined']) ? 'invalid' : '' ?>">
      <label for="joined">Joining Date</label>
      <input id="joined" name="joined" type="date" value="<?= e($old['joined']) ?>">
    </div>
  </div>
  <div class="actions">
    <button type="submit" class="btn primary">Save Employee</button>
    <a class="btn" href="index.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
