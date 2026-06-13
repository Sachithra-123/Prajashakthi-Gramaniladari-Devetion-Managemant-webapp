<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]);
require_once("../config/db.php");

$role  = $_SESSION["role"] ?? "";
$gn_id = $_SESSION["gn_id"] ?? 1;


$id = (int)($_GET["id"] ?? 0);
if($id <= 0) die("Invalid ID");


$stmt = $conn->prepare("SELECT * FROM projects WHERE project_id=? AND gn_id=? LIMIT 1");
if(!$stmt) die("Prepare failed (select): ".$conn->error);

$stmt->bind_param("ii",$id,$gn_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if(!$row) die("Project not found");

$error = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

  $project_title = trim($_POST["project_title"] ?? "");
  $project_type  = trim($_POST["project_type"] ?? "SHORT_TERM");
  $status        = trim($_POST["status"] ?? "PLANNED");

  $start_date = ($_POST["start_date"] ?? "") !== "" ? $_POST["start_date"] : null;
  $end_date   = ($_POST["end_date"] ?? "") !== "" ? $_POST["end_date"] : null;
  $budget     = ($_POST["budget"] ?? "") !== "" ? (float)$_POST["budget"] : null;

  if($project_title === ""){
    $error = "Project Title is required.";
  } else {

   
    $sql = "UPDATE projects SET
              project_title = ?,
              project_type  = ?,
              status        = ?,
              start_date    = ?,
              end_date      = ?,
              budget        = ?
            WHERE project_id = ? AND gn_id = ?";

    $up = $conn->prepare($sql);

   
    if(!$up){
      die("Prepare failed (update): " . $conn->error);
    }

    $up->bind_param(
      "sssssdii",
      $project_title,
      $project_type,
      $status,
      $start_date,
      $end_date,
      $budget,
      $id,
      $gn_id
    );

    if($up->execute()){
      header("Location: list.php");
      exit;
    } else {
      $error = "Update failed: " . $conn->error;
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Project</title>
<link rel="stylesheet" href="../assets/style.css">
</head>

<body>
<div class="shell">
<div class="wrapper">

<div class="sidebar">
  <div class="brand">Praja Shakthi</div>
  <div class="sub">GN Division Web System</div>

  <div class="nav">
    <a href="../dashboard/index.php">Dashboard</a>
    <a href="../projects/list.php" class="active">Projects</a>
  </div>

  <a class="logout" href="../auth/logout.php">Logout</a>
</div>

<div class="content">

<div class="topbar">
  <div><b>Edit Project</b></div>
  <div class="role"><?= htmlspecialchars($role) ?></div>
</div>

<div class="card">

<a class="btn dark" href="list.php">⬅ Back</a>

<?php if($error): ?>
  <div style="margin-top:12px;color:red;font-weight:800;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" style="margin-top:14px;">

<label>Project Title</label>
<input type="text" name="project_title" value="<?= htmlspecialchars($row["project_title"]) ?>" required>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
  <div>
    <label>Project Type</label>
    <select name="project_type">
      <?php
      foreach(["SHORT_TERM","LONG_TERM"] as $t){
        $sel = ($row["project_type"]==$t)?"selected":"";
        echo "<option value='$t' $sel>$t</option>";
      }
      ?>
    </select>
  </div>

  <div>
    <label>Status</label>
    <select name="status">
      <?php
      foreach(["PLANNED","ONGOING","COMPLETED"] as $s){
        $sel = ($row["status"]==$s)?"selected":"";
        echo "<option value='$s' $sel>$s</option>";
      }
      ?>
    </select>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
  <div>
    <label>Start Date</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($row["start_date"]) ?>">
  </div>

  <div>
    <label>End Date</label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($row["end_date"]) ?>">
  </div>
</div>

<label>Budget</label>
<input type="number" step="0.01" name="budget" value="<?= htmlspecialchars($row["budget"]) ?>">

<div style="display:flex;gap:10px;margin-top:16px;">
  <button type="submit" class="btn-update">Update Project</button>
  <a class="btn red" href="list.php">Cancel</a>
</div>

</form>

</div>
</div>
</div>
</div>
</body>
</html>