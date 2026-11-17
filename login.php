<?php
require __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif (isset(USERS[$username]) && password_verify($password, USERS[$username])) {
        session_regenerate_id(true);      // prevents session fixation
        $_SESSION['user'] = $username;
        flash('success', 'Welcome back, ' . $username . '!');
        redirect('index.php');
    } else {
        $error = 'Invalid username or password.';   // deliberately generic
    }
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>
<section class="panel narrow">
  <h1>Login</h1>
  <?php if ($error): ?><div class="alert error" role="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="login.php" novalidate>
    <div class="field">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" value="<?= e($username) ?>" autocomplete="username">
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password">
    </div>
    <div class="actions"><button type="submit" class="btn primary">Log In</button></div>
  </form>
  <p class="hint">Demo account: <code>admin</code> / <code>admin123</code></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
