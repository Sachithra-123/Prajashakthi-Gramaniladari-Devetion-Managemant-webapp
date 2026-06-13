<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]);
require_once("../config/db.php");

$role  = $_SESSION["role"] ?? "";
$gn_id = $_SESSION["gn_id"] ?? 1;


$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

$needs = [];
$stmtN = $conn->prepare("SELECT need_id, need_title FROM needs WHERE gn_id=? ORDER BY need_id DESC");
if(!$stmtN) die("Prepare failed (needs): " . $conn->error);
$stmtN->bind_param("i", $gn_id);
$stmtN->execute();
$needsRes = $stmtN->get_result();
while($n = $needsRes->fetch_assoc()){ $needs[] = $n; }

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

 
  $need_id = ($_POST["need_id"] !== "") ? (int)$_POST["need_id"] : null;

  $title  = trim($_POST["project_title"] ?? "");
  $type   = trim($_POST["project_type"] ?? "SHORT_TERM");   
  $status = trim($_POST["status"] ?? "PLANNED");            

  $start  = ($_POST["start_date"] ?? "") !== "" ? $_POST["start_date"] : null;
  $end    = ($_POST["end_date"] ?? "") !== "" ? $_POST["end_date"] : null;
  $budget = ($_POST["budget"] ?? "") !== "" ? (float)$_POST["budget"] : null;

  if ($title === "") {
    $error = "Project Title is required.";
  } else {

    
    $sql = "INSERT INTO projects (gn_id, need_id, project_title, project_type, status, start_date, end_date, budget)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if(!$stmt) die("Prepare failed (insert): " . $conn->error);

    
    $stmt->bind_param(
      "iisssssd",
      $gn_id,
      $need_id,
      $title,
      $type,
      $status,
      $start,
      $end,
      $budget
    );

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
  <title>Add Project</title>
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
    .two-col{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    @media (max-width:900px){.two-col{grid-template-columns:1fr;}}
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
        <a class="<?= active_path('/projects/') ?>" href="list.php">Projects</a>
        <a class="<?= active_path('/meetings/') ?>" href="../meetings/list.php">Meetings</a>
        <a class="<?= active_path('/reports/') ?>" href="../reports/index.php">Reports</a>
        <a class="<?= active_path('/users/') ?>" href="../users/list.php">User Management</a>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

 
    <div class="content">

      <div class="topbar">
        <div><b>Add Project</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">New Project</div>
          <a class="btn dark" href="list.php">⬅ Back</a>
        </div>

        <?php if($error): ?>
          <div class="msg-er"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">

          <label>Related Need (optional)</label>
          <select name="need_id">
            <option value="">-- No Need --</option>
            <?php foreach($needs as $n): ?>
              <option value="<?= (int)$n["need_id"] ?>">
                <?= htmlspecialchars($n["need_title"]) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <label>Project Title</label>
          <input type="text" name="project_title" required>

          <div class="two-col">
            <div>
              <label>Project Type</label>
              <select name="project_type" required>
                <option value="SHORT_TERM">SHORT_TERM</option>
                <option value="LONG_TERM">LONG_TERM</option>
              </select>
            </div>

            <div>
              <label>Status</label>
              <select name="status" required>
                <option value="PLANNED">PLANNED</option>
                <option value="ONGOING">ONGOING</option>
                <option value="COMPLETED">COMPLETED</option>
              </select>
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Start Date (optional)</label>
              <input type="date" name="start_date">
            </div>

            <div>
              <label>End Date (optional)</label>
              <input type="date" name="end_date">
            </div>
          </div>

          <label>Budget (optional)</label>
          <input type="number" step="0.01" name="budget" placeholder="e.g., 50000">

          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
            <button type="submit" class="btn-save">Save Project</button>
            <a class="btn red" href="list.php">Cancel</a>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
</body>
</html>