<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$role = $_SESSION["role"] ?? "";
$full_name = htmlspecialchars($_SESSION["full_name"] ?? "") 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
      <link rel="stylesheet" href="../assets/style.css">
    <style>
        body 
        { font-family: Arial;
         background:#f4f6f9; 
         padding: 20px; }

        .card { background:white; 
            padding:20px; 
            border-radius:10px; 
            max-width:700px; }

        a { display:inline-block; margin:6px 10px 0 0; }
    </style>
</head>
<body>
<div class="card">
  


    <h3>PRAJASHAKTHI </h3>

<div class="shell">
  <div class="wrapper">

    <div class="sidebar">
      <div class="brand">Praja Shakthi</div>
      <div class="sub">GN Division Web System</div>

      <div class="nav">
        
        <?php if ($role === "GN_OFFICER"): ?>
          <a class="active" href="../dashboard/index.php">Dashboard</a>
          <a href="../users/list.php">User Management</a>
          <a href="../gn_profile/view.php">GN Profile</a>
          <a href="../council/list.php">Council</a>
          <a href="../beneficiaries/list.php">Beneficiaries</a>
          <a href="../needs/list.php">Needs</a>
          <a href="../projects/list.php">Projects</a>
          <a href="../meetings/list.php">Meetings</a>
          <a href="../reports/index.php">Reports</a>

        <?php elseif ($role === "DIV_SECRETARIAT"): ?>
          <a class="active" href="../dashboard/index.php">Dashboard</a>
          <a href="../gn_profile/view.php">GN Profile</a>
          <a href="../beneficiaries/list.php">Beneficiaries</a>
          <a href="../projects/list.php">Projects</a>
          <a href="../reports/index.php">Reports</a>

        <?php elseif ($role === "COUNCIL"): ?>
          <a class="active" href="../dashboard/index.php">Dashboard</a>
          <a href="../council/list.php">Council</a>
          <a href="../projects/list.php">Projects</a>
          <a href="../meetings/list.php">Meetings</a>
          <a href="../reports/index.php">Reports</a>

        <?php else: ?>
          <a class="active" href="../dashboard/index.php">Dashboard</a>
          <a href="../projects/list.php">View Projects</a>
          <a href="../reports/index.php">View Reports</a>
        <?php endif; ?>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

    <div class="content">
      <div class="topbar">
        <div>Welcome, <b><?= htmlspecialchars($_SESSION["full_name"] ?? "") ?></b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">
        <h2>Dashboard</h2>
        <p>Select a module from the left menu.</p>
      </div>
    </div>

  </div>
</div>

</body>
</html>
