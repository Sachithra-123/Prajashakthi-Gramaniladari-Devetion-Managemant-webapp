<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER", "DIV_SECRETARIAT"]);
require_once("../config/db.php");

$role = $_SESSION["role"] ?? "";


$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}


$res = $conn->query("SELECT * FROM gn_division LIMIT 1");
if (!$res) {
  die("Query failed: " . $conn->error);
}
$gn = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>GN Division Profile</title>
  <link rel="stylesheet" href="../assets/style.css">

 
  <style>
    .profile-table td{
      padding:12px;
      border-bottom:1px solid #e6eaf0;
      vertical-align:top;
    }
    .profile-table td:first-child{
      width: 220px;
      font-weight:800;
      color:#0b1c2d;
      background:#f7f9fc;
    }
    .notice{
      background: rgba(220,53,69,0.12);
      border: 1px solid rgba(220,53,69,0.30);
      padding: 12px 14px;
      border-radius: 14px;
      color:#b02a37;
      font-weight:800;
      margin-top: 12px;
    }
  </style>
</head>

<body>
<div class="shell">
  <div class="wrapper">

 
    <div class="sidebar">
      <div class="brand">Praja Shakthi</div>
      <div class="sub">GN Division Web System</div>

      <div class="nav">
        <a class="<?= active_path('/dashboard/') ?>" href="../dashboard/index.php">Dashboard</a>

        <?php if ($role === "GN_OFFICER"): ?>
          <a class="<?= active_path('/users/') ?>" href="../users/list.php">User Management</a>
        <?php endif; ?>

        <a class="<?= active_path('/gn_profile/') ?>" href="view.php">GN Profile</a>
        <a class="<?= active_path('/beneficiaries/') ?>" href="../beneficiaries/list.php">Beneficiaries</a>
        <a class="<?= active_path('/needs/') ?>" href="../needs/list.php">Needs</a>
        <a class="<?= active_path('/projects/') ?>" href="../projects/list.php">Projects</a>
        <a class="<?= active_path('/meetings/') ?>" href="../meetings/list.php">Meetings</a>
        <a class="<?= active_path('/reports/') ?>" href="../reports/index.php">Reports</a>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

    <div class="content">

      <div class="topbar">
        <div><b>GN Division Profile</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">
            Profile Details
          </div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a class="btn dark" href="../dashboard/index.php">⬅ Dashboard</a>

            <?php if ($gn && $role === "GN_OFFICER"): ?>
              <a class="btn" href="edit.php?id=<?= (int)$gn["gn_id"] ?>">Edit Profile</a>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!$gn): ?>
          <div class="notice">No GN profile found in database.</div>
        <?php else: ?>

          <table class="profile-table" style="width:100%;border-collapse:collapse;border-radius:14px;overflow:hidden;">
            <tr><td>GN Name</td><td><?= htmlspecialchars($gn["gn_name"] ?? "") ?></td></tr>
            <tr><td>GN Number</td><td><?= htmlspecialchars($gn["gn_number"] ?? "") ?></td></tr>
            <tr><td>Population</td><td><?= htmlspecialchars($gn["population"] ?? "") ?></td></tr>
            <tr><td>Households</td><td><?= htmlspecialchars($gn["households_count"] ?? "") ?></td></tr>
            <tr><td>Phone</td><td><?= htmlspecialchars($gn["contact_phone"] ?? "") ?></td></tr>
            <tr><td>Email</td><td><?= htmlspecialchars($gn["contact_email"] ?? "") ?></td></tr>
            <tr><td>Address</td><td><?= nl2br(htmlspecialchars($gn["address"] ?? "")) ?></td></tr>
          </table>

        <?php endif; ?>

      </div>
    </div>

  </div>
</div>
</body>
</html>