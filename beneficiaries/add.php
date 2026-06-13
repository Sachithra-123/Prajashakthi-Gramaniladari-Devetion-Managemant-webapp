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

$success = "";
$error = "";

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

   
    if ($registered_date === "") {
      $registered_date = date("Y-m-d");
    }

    $stmt = $conn->prepare("INSERT INTO beneficiaries
      (gn_id, household_head, members_count, poverty_category, eligibility_status, registered_date, nic, phone, address)
      VALUES (?,?,?,?,?,?,?,?,?)");

    if (!$stmt) {
      die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
      "isissssss",
      $gn_id,
      $household_head,
      $members_count,
      $poverty_category,
      $eligibility_status,
      $registered_date,
      $nic,
      $phone,
      $address
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
  <title>Add Beneficiary</title>
  <link rel="stylesheet" href="../assets/style.css">

  <style>
    .msg-er{background: rgba(220,53,69,0.12);border:1px solid rgba(220,53,69,0.30);padding:12px 14px;border-radius:14px;color:#b02a37;font-weight:900;margin-bottom:14px;}
    .two-col{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    @media (max-width:900px){.two-col{grid-template-columns:1fr;}}
  </style>
</head>

<body>
<div class="shell">
  <div class="wrapper">

    <!-- SIDEBAR -->
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

    <!-- CONTENT -->
    <div class="content">

      <div class="topbar">
        <div><b>Add Beneficiary</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">New Beneficiary</div>
          <a class="btn dark" href="list.php">⬅ Back</a>
        </div>

        <?php if($error): ?><div class="msg-er"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST">

          <div class="two-col">
            <div>
              <label>Household Head</label>
              <input type="text" name="household_head" placeholder="Eg: Nimal Perera" required>
            </div>

            <div>
              <label>NIC (optional)</label>
              <input type="text" name="nic" placeholder="Eg: 200012345678">
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Members Count</label>
              <input type="number" name="members_count" value="1" min="0">
            </div>

            <div>
              <label>Poverty Category</label>
              <input type="text" name="poverty_category" placeholder="Eg: Low / Medium / High">
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Status</label>
              <select name="eligibility_status">
                <option value="Pending" selected>Pending</option>
                <option value="Eligible">Eligible</option>
                <option value="Not Eligible">Not Eligible</option>
              </select>
            </div>

            <div>
              <label>Registered Date</label>
              <input type="date" name="registered_date" value="<?= date("Y-m-d") ?>">
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Phone (optional)</label>
              <input type="text" name="phone" placeholder="Eg: 07XXXXXXXX">
            </div>

            <div>
              <label>Address (optional)</label>
              <input type="text" name="address" placeholder="Eg: GN Division Address">
            </div>
          </div>

          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
            <button type="submit" class="btn-save">Save Beneficiary</button>
            <a class="btn red" href="list.php">Cancel</a>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
</body>
</html>