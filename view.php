<?php
require __DIR__ . '/includes/functions.php';
require_login();

// GET parameter: validate before using it
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$employee = $id ? find_employee($id) : null;

if (!$employee) {
    http_response_code(404);
    flash('error', 'Employee not found.');
    redirect('index.php');
}

$pageTitle = $employee['name'];
require __DIR__ . '/includes/header.php';
?>
<div class="title-row">
  <h1>Employee Details</h1>
  <a class="btn" href="index.php">&larr; Back to list</a>
</div>

<section class="panel">
  <dl class="details">
    <dt>Employee ID</dt>      <dd><?= (int) $employee['id'] ?></dd>
    <dt>Full Name</dt>        <dd><?= e($employee['name']) ?></dd>
    <dt>Email</dt>            <dd><?= e($employee['email']) ?></dd>
    <dt>Department</dt>       <dd><?= e($employee['department']) ?></dd>
    <dt>Designation</dt>      <dd><?= e($employee['designation']) ?></dd>
    <dt>Monthly Salary</dt>   <dd>Rs. <?= number_format((float) $employee['salary']) ?></dd>
    <dt>Joining Date</dt>     <dd><?= e(date('d M Y', strtotime($employee['joined']))) ?></dd>
  </dl>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
