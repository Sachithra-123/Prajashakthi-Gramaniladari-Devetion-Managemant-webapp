<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]); 
require_once("../config/db.php");

if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }

$role    = $_SESSION["role"] ?? "";
$gn_id   = $_SESSION["gn_id"] ?? 1;
$user_id = $_SESSION["user_id"] ?? 0;

/* sidebar active highlight */
$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $meeting_date_raw = trim($_POST["meeting_date"] ?? ""); 
  $location = trim($_POST["location"] ?? "");
  $agenda   = trim($_POST["agenda"] ?? "");

  if ($meeting_date_raw === "") {
    $error = "Meeting Date & Time is required.";
  } else {
  
    $meeting_date = str_replace("T", " ", $meeting_date_raw);
    if (strlen($meeting_date) === 16) {
      $meeting_date .= ":00";
    }

    $stmt = $conn->prepare("INSERT INTO meetings (gn_id, meeting_date, location, agenda, created_by)
                            VALUES (?, ?, ?, ?, ?)");
    if(!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("isssi", $gn_id, $meeting_date, $location, $agenda, $user_id);

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
  <title>Add Meeting</title>
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
        <a href="../dashboard/index.php">Dashboard</a>
        <a href="../gn_profile/view.php">GN Profile</a>
        <a href="../beneficiaries/list.php">Beneficiaries</a>
        <a href="../needs/list.php">Needs</a>
        <a href="../projects/list.php">Projects</a>
        <a class="active" href="list.php">Meetings</a>
        <a href="../reports/index.php">Reports</a>
        <a href="../users/list.php">User Management</a>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

    
    <div class="content">

      <div class="topbar">
        <div><b>Add Meeting</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">New Meeting</div>
          <a class="btn dark" href="list.php">⬅ Back</a>
        </div>

        <?php if($error): ?>
          <div class="msg-er"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">

          <label>Date & Time</label>
          <input type="datetime-local" name="meeting_date" required>

          <label>Location</label>
          <input type="text" name="location" placeholder="Eg: GN Office / Community Hall">

          <label>Agenda</label>
          <textarea name="agenda" rows="4" placeholder="Write meeting agenda..."></textarea>

          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
            <button type="submit" class="btn-save">Save Meeting</button>
            <a class="btn red" href="list.php">Cancel</a>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
</body>
</html>