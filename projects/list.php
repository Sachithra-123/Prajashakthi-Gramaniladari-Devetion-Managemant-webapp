<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER","DIV_SECRETARIAT","COUNCIL"]);
require_once("../config/db.php");

$role  = $_SESSION["role"] ?? "";
$gn_id = $_SESSION["gn_id"] ?? 1;

$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

$res = $conn->prepare("SELECT * FROM projects WHERE gn_id=? ORDER BY project_id DESC");
$res->bind_param("i",$gn_id);
$res->execute();
$list = $res->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Projects</title>
  <link rel="stylesheet" href="../assets/style.css">
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
    <a class="<?= active_path('/projects/') ?>" href="list.php">Projects</a>
    <a class="<?= active_path('/needs/') ?>" href="../needs/list.php">Needs</a>
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
  <div><b>Projects</b></div>
  <div class="role"><?= htmlspecialchars($role) ?></div>
</div>

<div class="card">

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;margin-bottom:14px;">
  <?php if($role==="GN_OFFICER"): ?>
    <a class="btn green" href="add.php">+ Add Project</a>
  <?php endif; ?>
  <a class="btn dark" href="../dashboard/index.php">⬅ Dashboard</a>
</div>

<table>
<thead>
<tr>
  <th>Project Name</th>
  <th>Category</th>
  <th>Status</th>
  <th>Start Date</th>
  <th>End Date</th>
  <th style="width:160px;">Actions</th>
</tr>
</thead>
<tbody>

<?php if($list->num_rows==0): ?>
<tr><td colspan="6">No projects found.</td></tr>
<?php else: ?>
<?php while($row=$list->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($row["project_title"]) ?></td>
  <td><?= htmlspecialchars($row["project_type"]) ?></td>
  <td><?= htmlspecialchars($row["status"]) ?></td>
  <td><?= htmlspecialchars($row["start_date"]) ?></td>
  <td><?= htmlspecialchars($row["end_date"]) ?></td>
  <td>
    <?php if($role==="GN_OFFICER"): ?>
      <div style="display:flex;gap:8px;">
        <a class="btn" href="edit.php?id=<?= (int)$row["project_id"] ?>">Edit</a>
        <a class="btn red" href="delete.php?id=<?= (int)$row["project_id"] ?>"
           onclick="return confirm('Delete this project?')">Delete</a>
      </div>
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