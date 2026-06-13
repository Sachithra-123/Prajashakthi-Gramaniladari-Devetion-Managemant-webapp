<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]); 
require_once("../config/db.php");

if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }

$role  = $_SESSION["role"] ?? "";
$gn_id = $_SESSION["gn_id"] ?? 1;


$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $title = trim($_POST["need_title"] ?? "");
  $desc  = trim($_POST["need_description"] ?? "");
  $prio  = trim($_POST["priority_level"] ?? "MEDIUM");

  if ($title === "") {
    $error = "Need Title is required.";
  } else {

    $stmt = $conn->prepare("INSERT INTO needs
      (gn_id, need_title, need_description, priority_level)
      VALUES (?, ?, ?, ?)");
    if(!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("isss", $gn_id, $title, $desc, $prio);

    if ($stmt->execute()) {
      header("Location: list.php");
      exit;
    } else {
      $error = "Insert failed: " . $conn->error;
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add Community Need</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .msg-er{
      background: rgba(220,53,69,0.12);
      border: 1px solid rgba(220,53,69,0.30);
      padding: 12px 14px;
      border-radius: 14px;
      color:#b02a37;
      font-weight:900;
      margin-bottom: 14px;
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
        <a class="<?= active_path('/gn_profile/') ?>" href="../gn_profile/view.php">GN Profile</a>
        <a class="<?= active_path('/beneficiaries/') ?>" href="../beneficiaries/list.php">Beneficiaries</a>
        <a class="<?= active_path('/needs/') ?>" href="list.php">Needs</a>
        <a class="<?= active_path('/projects/') ?>" href="../projects/list.php">Projects</a>
        <a class="<?= active_path('/meetings/') ?>" href="../meetings/list.php">Meetings</a>
        <a class="<?= active_path('/reports/') ?>" href="../reports/index.php">Reports</a>
        <?php if($role==="GN_OFFICER"): ?>
          <a class="<?= active_path('/users/') ?>" href="../users/list.php">User Management</a>
        <?php endif; ?>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

   
    <div class="content">

      <div class="topbar">
        <div><b>Add Community Need</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">New Community Need</div>
          <a class="btn dark" href="list.php">⬅ Back</a>
        </div>

        <?php if($error): ?>
          <div class="msg-er"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">

          <label>Need Title</label>
          <input type="text" name="need_title" placeholder="Eg: Drinking Water Supply" required>

          <label>Description</label>
          <textarea name="need_description" rows="4" placeholder="Describe the community need..."></textarea>

          <label>Priority</label>
          <select name="priority_level">
            <option value="HIGH">HIGH</option>
            <option value="MEDIUM" selected>MEDIUM</option>
            <option value="LOW">LOW</option>
          </select>

          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
            <button type="submit" class="btn-save">Save Need</button>
            <a class="btn red" href="list.php">Cancel</a>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
</body>
</html>