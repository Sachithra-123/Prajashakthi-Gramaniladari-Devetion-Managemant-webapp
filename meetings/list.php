<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER", "COUNCIL"]);
require_once("../config/db.php");

if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }

$role  = $_SESSION["role"] ?? "";
$gn_id = $_SESSION["gn_id"] ?? 1;


$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

$stmt = $conn->prepare("SELECT * FROM meetings WHERE gn_id=? ORDER BY meeting_id DESC");
if(!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("i", $gn_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Meetings</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .top-actions{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      margin-bottom:14px;
    }
    .muted{ color:#666; font-weight:700; }
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
        <a class="<?= active_path('/gn_profile/') ?>" href="../gn_profile/view.php">GN Profile</a>
        <a class="<?= active_path('/beneficiaries/') ?>" href="../beneficiaries/list.php">Beneficiaries</a>
        <a class="<?= active_path('/needs/') ?>" href="../needs/list.php">Needs</a>
        <a class="<?= active_path('/projects/') ?>" href="../projects/list.php">Projects</a>
        <a class="<?= active_path('/meetings/') ?>" href="list.php">Meetings</a>
        <a class="<?= active_path('/reports/') ?>" href="../reports/index.php">Reports</a>
        <?php if($role==="GN_OFFICER"): ?>
          <a class="<?= active_path('/users/') ?>" href="../users/list.php">User Management</a>
        <?php endif; ?>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>


    <div class="content">

      <div class="topbar">
        <div><b>Meetings</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div class="top-actions">
          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <?php if($role === "GN_OFFICER"): ?>
              <a class="btn green" href="add.php">+ Add Meeting</a>
            <?php endif; ?>
            <a class="btn dark" href="../dashboard/index.php">⬅ Dashboard</a>
          </div>

          <div class="muted">
            Minutes can be uploaded per meeting
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Date & Time</th>
              <th>Location</th>
              <th>Agenda</th>
              <th>Minutes</th>
              <th style="width:220px;">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($res->num_rows === 0): ?>
            <tr><td colspan="5">No meetings found.</td></tr>
          <?php else: ?>
            <?php while($m = $res->fetch_assoc()): ?>
              <?php $mid = (int)($m["meeting_id"] ?? 0); ?>
              <tr>
                <td><?= htmlspecialchars($m["meeting_date"] ?? "") ?></td>
                <td><?= htmlspecialchars($m["location"] ?? "") ?></td>
                <td><?= htmlspecialchars($m["agenda"] ?? "") ?></td>
                <td>
                  <a class="btn green" href="minutes_upload.php?meeting_id=<?= $mid ?>">Upload</a>
                </td>
                <td>
                  <?php if($role === "GN_OFFICER"): ?>
                    <div style="display:flex;gap:8px;flex-wrap:nowrap;">
                      <a class="btn" href="edit.php?id=<?= $mid ?>">Edit</a>
                      <a class="btn red" href="delete.php?id=<?= $mid ?>"
                         onclick="return confirm('Delete meeting?')">Delete</a>
                    </div>
                  <?php else: ?>
                    <span class="muted">View only</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
          </tbody>
        </table>

      </div>
    </div>

  </div>
</div>
</body>
</html>