<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]);
require_once("../config/db.php");

if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }

$role  = $_SESSION["role"] ?? "";
$gn_id = $_SESSION["gn_id"] ?? 1;

$id = (int)($_GET["id"] ?? 0);
if($id <= 0) die("Invalid ID");


$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}


$stmt = $conn->prepare("SELECT * FROM meetings WHERE meeting_id=? AND gn_id=? LIMIT 1");
if(!$stmt) die("Prepare failed (select): " . $conn->error);
$stmt->bind_param("ii", $id, $gn_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if(!$row) die("Meeting not found");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $meeting_date_raw = trim($_POST["meeting_date"] ?? ""); 
  $location = trim($_POST["location"] ?? "");
  $agenda   = trim($_POST["agenda"] ?? "");

  if ($meeting_date_raw === "") {
    $error = "Meeting Date & Time is required.";
  } else {

    
    $meeting_date = str_replace("T", " ", $meeting_date_raw);
    if (strlen($meeting_date) === 16) { $meeting_date .= ":00"; }

    $up = $conn->prepare("UPDATE meetings
                          SET meeting_date=?, location=?, agenda=?
                          WHERE meeting_id=? AND gn_id=?");
    if(!$up) die("Prepare failed (update): " . $conn->error);

    $up->bind_param("sssii", $meeting_date, $location, $agenda, $id, $gn_id);

    if($up->execute()){
      header("Location: list.php");
      exit;
    } else {
      $error = "Update failed: " . $conn->error;
    }
  }
}


$dt_local = "";
if(!empty($row["meeting_date"])) {
  
  $dt_local = str_replace(" ", "T", substr($row["meeting_date"], 0, 16));
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Meeting</title>
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
        <a class="<?= active_path('/needs/') ?>" href="../needs/list.php">Needs</a>
        <a class="<?= active_path('/projects/') ?>" href="../projects/list.php">Projects</a>
        <a class="<?= active_path('/meetings/') ?>" href="list.php">Meetings</a>
        <a class="<?= active_path('/reports/') ?>" href="../reports/index.php">Reports</a>
        <a class="<?= active_path('/users/') ?>" href="../users/list.php">User Management</a>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

    
    <div class="content">

      <div class="topbar">
        <div><b>Edit Meeting</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">Update Meeting</div>
          <a class="btn dark" href="list.php">⬅ Back</a>
        </div>

        <?php if($error): ?>
          <div class="msg-er"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">

          <label>Date & Time</label>
          <input type="datetime-local" name="meeting_date" value="<?= htmlspecialchars($dt_local) ?>" required>

          <label>Location</label>
          <input type="text" name="location" value="<?= htmlspecialchars($row["location"] ?? "") ?>" placeholder="Eg: GN Office / Community Hall">

          <label>Agenda</label>
          <textarea name="agenda" rows="4" placeholder="Write meeting agenda..."><?= htmlspecialchars($row["agenda"] ?? "") ?></textarea>

          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
            <button type="submit" class="btn-update">Update Meeting</button>
            <a class="btn red" href="list.php">Cancel</a>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
</body>
</html>