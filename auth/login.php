<?php
session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: ../dashboard/index.php");
    exit;
}

$error = "";
if (isset($_GET["error"])) {
    $error = "Invalid username or password!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Praja Shakthi - Login</title>

    
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body class="login-body">

  <div class="login-card">

    <h2>Praja Shakthi</h2>
    <div class="login-sub">GN Division Web System</div>

    <?php if ($error): ?>
      <div class="login-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="authenticate.php">

      <label>Username</label>
      <input type="text" name="username" placeholder="Enter username" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Enter password" required>

      <button type="submit" class="login-btn">LOGIN</button>

    </form>

    <p style="text-align:center;margin-top:18px;color:rgba(255,255,255,0.8);font-size:13px;">
      Roles: GN Officer / Council / Divisional Secretariat / Community
    </p>

  </div>

</body>
</html>
