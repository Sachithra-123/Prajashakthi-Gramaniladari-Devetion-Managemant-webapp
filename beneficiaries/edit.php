<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]); 
require_once("../config/db.php");

$role  = $_SESSION["role"] ?? "";
$gn_id = $_SESSION["gn_id"] ?? 1;

/* sidebar active highlight */
$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

/* get id */
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) { die("Invalid ID."); }

/* fetch record */
$stmt = $conn->prepare("SELECT * FROM beneficiaries WHERE beneficiary_id=? AND gn_id=? LIMIT 1");
if (!$stmt) { die("Prepare failed: " . $conn->error); }
$stmt->bind_param("ii", $id, $gn_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { die("Beneficiary not found."); }

$success = "";
$error   = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

  
  $household_head   = trim($_POST["household_head"] ?? "");
  $nic              = trim($_POST["nic"] ?? "");
  $members_count    = (int)($_POST["members_count"] ?? 0);
  $poverty_category = trim($_POST["poverty_category"] ?? "");
  $eligibility_status = trim($_POST["eligibility_status"] ?? "Pending");
  $registered_date  = trim($_POST["registered_date"] ?? "");
  $phone            = trim($_POST["phone"] ?? "");
  $address          = trim($_POST["address"] ?? "");

  if ($household_head === "") {
    $error = "Household Head is required.";
  } else {

    $up = $conn->prepare("UPDATE beneficiaries
                          SET household_head=?,
                              nic=?,
                              members_count=?,
                              poverty_category=?,
                              eligibility_status=?,
                              registered_date=?,
                              phone=?,
                              address=?
                          WHERE beneficiary_id=? AND gn_id=?");
    if (!$up) { die("Prepare failed: " . $conn->error); }

    $up->bind_param(
      "ssissssssi",
      $household_head,
      $nic,
      $members_count,
      $poverty_category,
      $eligibility_status,
      $registered_date,
      $phone,
      $address,
      $id,
      $gn_id
    );

    if ($up->execute()) {
      $success = "Beneficiary updated successfully!";

    
      $stmt = $conn->prepare("SELECT * FROM beneficiaries WHERE beneficiary_id=? AND gn_id=? LIMIT 1");
      $stmt->bind_param("ii", $id, $gn_id);
      $stmt->execute();
      $row = $stmt->get_result()->fetch_assoc();
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
  <title>Edit Beneficiary</title>
  <link rel="stylesheet" href="../assets/style.css">

  <style>
    .msg-ok{background: rgba(25,135,84,0.12);border:1px solid rgba(25,135,84,0.30);padding:12px 14px;border-radius:14px;color:#146c43;font-weight:900;margin-bottom:14px;}
    .msg-er{background: rgba(220,53,69,0.12);border:1px solid rgba(220,53,69,0.30);padding:12px 14px;border-radius:14px;color:#b02a37;font-weight:900;margin-bottom:14px;}
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
        <a class="<?= active_path('/beneficiaries/') ?>" href="list.php">Beneficiaries</a>
        <a class="<?= active_path('/projects/') ?>" href="../projects/list.php">Projects</a>
        <a class="<?= active_path('/needs/') ?>" href="../needs/list.php">Needs</a>
        <a class="<?= active_path('/meetings/') ?>" href="../meetings/list.php">Meetings</a>
        <a class="<?= active_path('/reports/') ?>" href="../reports/index.php">Reports</a>
        <a class="<?= active_path('/users/') ?>" href="../users/list.php">User Management</a>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

   
    <div class="content">

      <div class="topbar">
        <div><b>Edit Beneficiary</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">Update Details</div>
        
          <a class="btn dark" href="list.php">⬅ Back</a>
        </div>

        <?php if($success): ?><div class="msg-ok"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if($error): ?><div class="msg-er"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST">

          <div class="two-col">
            <div>
              <label>Household Head</label>
              <input type="text" name="household_head" value="<?= htmlspecialchars($row["household_head"] ?? "") ?>" required>
            </div>

            <div>
              <label>NIC</label>
              <input type="text" name="nic" value="<?= htmlspecialchars($row["nic"] ?? "") ?>">
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Members Count</label>
              <input type="number" name="members_count" value="<?= htmlspecialchars($row["members_count"] ?? "0") ?>">
            </div>

            <div>
              <label>Poverty Category</label>
              <input type="text" name="poverty_category" value="<?= htmlspecialchars($row["poverty_category"] ?? "") ?>">
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Status</label>
              <select name="eligibility_status">
                <?php
                  $st = $row["eligibility_status"] ?? "Pending";
                  $opts = ["Eligible","Not Eligible","Pending"];
                  foreach($opts as $o){
                    $sel = ($st === $o) ? "selected" : "";
                    echo "<option value=\"".htmlspecialchars($o)."\" $sel>".htmlspecialchars($o)."</option>";
                  }
                ?>
              </select>
            </div>

            <div>
              <label>Registered Date</label>
              <input type="date" name="registered_date" value="<?= htmlspecialchars($row["registered_date"] ?? "") ?>">
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Phone</label>
              <input type="text" name="phone" value="<?= htmlspecialchars($row["phone"] ?? "") ?>">
            </div>

            <div>
              <label>Address</label>
              <input type="text" name="address" value="<?= htmlspecialchars($row["address"] ?? "") ?>">
            </div>
          </div>

          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
            <button type="submit" class="btn-update">Update Beneficiary</button>
           
            <a class="btn red" href="list.php">Cancel</a>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
</body>
</html>