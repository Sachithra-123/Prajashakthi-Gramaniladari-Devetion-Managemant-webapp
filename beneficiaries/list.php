<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER", "DIV_SECRETARIAT"]);
require_once("../config/db.php");

$role  = $_SESSION["role"] ?? "";
$gn_id = $_SESSION["gn_id"] ?? 1;

$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

$q = trim($_GET["q"] ?? "");


if ($q !== "") {
  $stmt = $conn->prepare("SELECT * FROM beneficiaries
                          WHERE gn_id=? AND household_head LIKE CONCAT('%', ?, '%')
                          ORDER BY beneficiary_id DESC");
  if(!$stmt) die("Prepare failed: ".$conn->error);
  $stmt->bind_param("is", $gn_id, $q);
} else {
  $stmt = $conn->prepare("SELECT * FROM beneficiaries
                          WHERE gn_id=?
                          ORDER BY beneficiary_id DESC");
  if(!$stmt) die("Prepare failed: ".$conn->error);
  $stmt->bind_param("i", $gn_id);
}
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Beneficiaries</title>
  <link rel="stylesheet" href="../assets/style.css">

  <style>
    .status-pill{
      display:inline-block;
      padding:4px 10px;
      border-radius:999px;
      font-size:12px;
      font-weight:900;
      background:#0b1c2d;
      color:#f1c40f;
      white-space:nowrap;
    }
    .top-actions{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      justify-content:space-between;
      align-items:center;
      margin-bottom:14px;
    }
    .search-form{
      display:flex;
      gap:10px;
      align-items:center;
      flex-wrap:wrap;
    }
    .search-form input{ max-width:260px; }
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
        <a class="<?= active_path('/beneficiaries/') ?>" href="list.php">Beneficiaries</a>
        <a class="<?= active_path('/projects/') ?>" href="../projects/list.php">Projects</a>
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
        <div><b>Beneficiaries</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div class="top-actions">
          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <?php if($role === "GN_OFFICER"): ?>
              <a class="btn green" href="add.php">+ Add Beneficiary</a>
            <?php endif; ?>
            <a class="btn dark" href="../dashboard/index.php">⬅ Dashboard</a>
          </div>

          <form class="search-form" method="GET">
            <input type="text" name="q" placeholder="Search household head..." value="<?= htmlspecialchars($q) ?>">
            <button type="submit">Search</button>
            <?php if($q !== ""): ?>
              <a class="btn" href="list.php">Clear</a>
            <?php endif; ?>
          </form>
        </div>

        <table>
          <thead>
            <tr>
              <th>Household Head</th>
              <th>Members</th>
              <th>Poverty Category</th>
              <th>Status</th>
              <th>Registered</th>
              <th style="width:180px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($res->num_rows === 0): ?>
              <tr><td colspan="6">No beneficiaries found.</td></tr>
            <?php else: ?>
              <?php while($row = $res->fetch_assoc()): ?>
                <?php $bid = (int)($row["beneficiary_id"] ?? 0); ?>
                <tr>
                  <td><?= htmlspecialchars($row["household_head"] ?? "") ?></td>
                  <td><?= htmlspecialchars($row["members_count"] ?? "") ?></td>
                  <td><?= htmlspecialchars($row["poverty_category"] ?? "") ?></td>
                  <td><span class="status-pill"><?= htmlspecialchars($row["eligibility_status"] ?? "") ?></span></td>
                  <td><?= htmlspecialchars($row["registered_date"] ?? "") ?></td>
                  <td>
                    
                    

                    <?php if($role === "GN_OFFICER"): ?>
  <div style="display:flex;gap:8px;flex-wrap:nowrap;">
    <a class="btn" href="edit.php?id=<?= $bid ?>">Edit</a>
    <a class="btn red" href="delete.php?id=<?= $bid ?>"
       onclick="return confirm('Delete this beneficiary?')">Delete</a>
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