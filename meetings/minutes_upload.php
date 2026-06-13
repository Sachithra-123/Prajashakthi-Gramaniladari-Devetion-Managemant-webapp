<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER","COUNCIL"]);
require_once("../config/db.php");

if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }

$role    = $_SESSION["role"] ?? "";
$gn_id   = $_SESSION["gn_id"] ?? 1;
$user_id = $_SESSION["user_id"] ?? 0;


$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

$meeting_id = (int)($_GET["meeting_id"] ?? 0);
if ($meeting_id <= 0) die("Invalid meeting id");


$chk = $conn->prepare("SELECT meeting_id FROM meetings WHERE meeting_id=? AND gn_id=? LIMIT 1");
if(!$chk) die("Prepare failed: " . $conn->error);
$chk->bind_param("ii", $meeting_id, $gn_id);
$chk->execute();
$chkRes = $chk->get_result();
if ($chkRes->num_rows !== 1) die("Meeting not found");


$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $role === "GN_OFFICER") {

  if (!isset($_FILES["minutes_file"]) || $_FILES["minutes_file"]["error"] !== UPLOAD_ERR_OK) {
    $err = "File upload failed.";
  } else {

    $allowed = ["pdf","jpg","jpeg","png"];
    $name = $_FILES["minutes_file"]["name"];
    $tmp  = $_FILES["minutes_file"]["tmp_name"];
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
      $err = "Only PDF, JPG, JPEG, PNG files allowed.";
    } else {

      $newName = "minutes_" . $meeting_id . "_" . time() . "." . $ext;
      $dest = "../uploads/minutes/" . $newName;

      if (!move_uploaded_file($tmp, $dest)) {
        $err = "Could not save file.";
      } else {

        $ins = $conn->prepare("INSERT INTO meeting_minutes (meeting_id, file_path, uploaded_by)
                               VALUES (?, ?, ?)");
        if(!$ins) die("Prepare failed: " . $conn->error);

        $ins->bind_param("isi", $meeting_id, $dest, $user_id);
        $ins->execute();

        $msg = "Minutes uploaded successfully!";
      }
    }
  }
}


$stmt = $conn->prepare("SELECT * FROM meeting_minutes WHERE meeting_id=? ORDER BY minute_id DESC");
$stmt->bind_param("i", $meeting_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Meeting Minutes</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .msg-ok{
      background: rgba(25,135,84,0.12);
      border:1px solid rgba(25,135,84,0.30);
      padding:12px 14px;
      border-radius:14px;
      color:#146c43;
      font-weight:900;
      margin-bottom:14px;
    }
    .msg-er{
      background: rgba(220,53,69,0.12);
      border:1px solid rgba(220,53,69,0.30);
      padding:12px 14px;
      border-radius:14px;
      color:#b02a37;
      font-weight:900;
      margin-bottom:14px;
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
        <?php if($role==="GN_OFFICER"): ?>
          <a class="<?= active_path('/users/') ?>" href="../users/list.php">User Management</a>
        <?php endif; ?>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

  
    <div class="content">

      <div class="topbar">
        <div><b>Meeting Minutes</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">Meeting #<?= $meeting_id ?></div>
          <a class="btn dark" href="list.php">⬅ Back to Meetings</a>
        </div>

        <?php if($msg): ?><div class="msg-ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if($err): ?><div class="msg-er"><?= htmlspecialchars($err) ?></div><?php endif; ?>

        <?php if($role === "GN_OFFICER"): ?>
        <h3 style="margin-top:0;">Upload Minutes</h3>

        <form method="POST" enctype="multipart/form-data" style="margin-bottom:20px;">
          <input type="file" name="minutes_file" required>
          <button type="submit" class="btn-save">Upload File</button>
        </form>
        <?php endif; ?>

        <h3>Uploaded Files</h3>

        <table>
          <thead>
            <tr>
              <th>File</th>
              <th>Uploaded At</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($res->num_rows === 0): ?>
            <tr><td colspan="2">No minutes uploaded yet.</td></tr>
          <?php else: ?>
            <?php while($r = $res->fetch_assoc()): ?>
              <tr>
                <td>
                  <a href="<?= htmlspecialchars($r["file_path"]) ?>" target="_blank">
                    📄 Open / Download
                  </a>
                </td>
                <td><?= htmlspecialchars($r["uploaded_at"]) ?></td>
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