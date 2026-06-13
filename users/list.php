<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }

if (($_SESSION["role"] ?? "") !== "GN_OFFICER") {
    die("Access denied");
}

require_once("../config/db.php");

$gn_id = $_SESSION["gn_id"] ?? 1;


$stmt = $conn->prepare("SELECT user_id, full_name, username, role, status
                        FROM users
                        WHERE gn_id=?
                        ORDER BY user_id DESC");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $gn_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>User Management</title>
    <link rel="stylesheet" href="../assets/style.css">
  <style>
    body{font-family:Arial;background:#f4f6f9;padding:20px;}
    .box{background:#fff;padding:20px;border-radius:10px;max-width:1000px;}
    table{width:100%;border-collapse:collapse;margin-top:10px;}
    th,td{padding:10px;border-bottom:1px solid #ddd;text-align:left;}
    a.btn{padding:8px 12px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:6px;margin-right:6px;}
    a.danger{background:#dc3545;}
    .badge{padding:3px 8px;border-radius:999px;background:#eee;font-size:12px;}
  </style>
</head>
<body>
<div class="box">
  <h2>User Management (GN Officer)</h2>
  <a class="btn" href="add.php">+ Add User</a>
  <a class="btn" href="../dashboard/index.php">Dashboard</a>

  <table>
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if ($res->num_rows === 0): ?>
      <tr><td colspan="5">No users found.</td></tr>
    <?php else: ?>
      <?php while($u = $res->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($u["full_name"]) ?></td>
          <td><?= htmlspecialchars($u["username"]) ?></td>
          <td><span class="badge"><?= htmlspecialchars($u["role"]) ?></span></td>
          <td><?= ((int)$u["status"]===1) ? "ACTIVE" : "INACTIVE" ?></td>
          <td>
            <?php if ((int)$u["user_id"] !== (int)$_SESSION["user_id"]): ?>
              <a class="btn" href="toggle_status.php?id=<?= (int)$u["user_id"] ?>">
                <?= ((int)$u["status"]===1) ? "Deactivate" : "Activate" ?>
              </a>
              <a class="btn danger" href="delete.php?id=<?= (int)$u["user_id"] ?>"
                 onclick="return confirm('Delete this user?')">Delete</a>
            <?php else: ?>
              (You)
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
