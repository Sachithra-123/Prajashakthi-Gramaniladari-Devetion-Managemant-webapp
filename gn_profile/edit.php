<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]); 
require_once("../config/db.php");

$role = $_SESSION["role"] ?? "";


$path = $_SERVER["PHP_SELF"];
function active_path($needle){
  global $path;
  return (strpos($path, $needle) !== false) ? "active" : "";
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
  die("Invalid GN ID.");
}


$stmt = $conn->prepare("SELECT * FROM gn_division WHERE gn_id=? LIMIT 1");
if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("i", $id);
$stmt->execute();
$gn = $stmt->get_result()->fetch_assoc();

if (!$gn) {
  die("GN profile not found.");
}

$success = "";
$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $gn_name = trim($_POST["gn_name"] ?? "");
  $gn_number = trim($_POST["gn_number"] ?? "");
  $population = trim($_POST["population"] ?? "");
  $households_count = trim($_POST["households_count"] ?? "");
  $contact_phone = trim($_POST["contact_phone"] ?? "");
  $contact_email = trim($_POST["contact_email"] ?? "");
  $address = trim($_POST["address"] ?? "");

  if ($gn_name === "" || $gn_number === "") {
    $error = "GN Name and GN Number are required.";
  } else {
    $up = $conn->prepare("UPDATE gn_division
                          SET gn_name=?, gn_number=?, population=?, households_count=?,
                              contact_phone=?, contact_email=?, address=?
                          WHERE gn_id=?");
    if (!$up) die("Prepare failed: " . $conn->error);
    $up->bind_param(
      "ssissssi",
      $gn_name,
      $gn_number,
      $population,
      $households_count,
      $contact_phone,
      $contact_email,
      $address,
      $id
    );

    if ($up->execute()) {
      $success = "Profile updated successfully!";
    
      $stmt = $conn->prepare("SELECT * FROM gn_division WHERE gn_id=? LIMIT 1");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $gn = $stmt->get_result()->fetch_assoc();
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
  <title>Edit GN Profile</title>
  <link rel="stylesheet" href="../assets/style.css">

  <style>
    .msg-ok{
      background: rgba(25,135,84,0.12);
      border: 1px solid rgba(25,135,84,0.30);
      padding: 12px 14px;
      border-radius: 14px;
      color:#146c43;
      font-weight:800;
      margin-bottom: 14px;
    }
    .msg-err{
      background: rgba(220,53,69,0.12);
      border: 1px solid rgba(220,53,69,0.30);
      padding: 12px 14px;
      border-radius: 14px;
      color:#b02a37;
      font-weight:800;
      margin-bottom: 14px;
    }
    .two-col{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }
    @media (max-width: 900px){
      .two-col{ grid-template-columns: 1fr; }
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
        <a class="<?= active_path('/users/') ?>" href="../users/list.php">User Management</a>
        <a class="<?= active_path('/gn_profile/') ?>" href="view.php">GN Profile</a>
        <a class="<?= active_path('/beneficiaries/') ?>" href="../beneficiaries/list.php">Beneficiaries</a>
        <a class="<?= active_path('/needs/') ?>" href="../needs/list.php">Needs</a>
        <a class="<?= active_path('/projects/') ?>" href="../projects/list.php">Projects</a>
        <a class="<?= active_path('/meetings/') ?>" href="../meetings/list.php">Meetings</a>
        <a class="<?= active_path('/reports/') ?>" href="../reports/index.php">Reports</a>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

  
    <div class="content">

      <div class="topbar">
        <div><b>Edit GN Profile</b></div>
        <div class="role"><?= htmlspecialchars($role) ?></div>
      </div>

      <div class="card">

        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
          <div style="font-size:20px;font-weight:900;color:#0b1c2d;">Update Profile Details</div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a class="btn dark" href="view.php">⬅ Back</a>
          </div>
        </div>

        <?php if ($success): ?>
          <div class="msg-ok"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="msg-err"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">

          <div class="two-col">
            <div>
              <label>GN Name</label>
              <input type="text" name="gn_name" value="<?= htmlspecialchars($gn["gn_name"]) ?>" required>
            </div>

            <div>
              <label>GN Number</label>
              <input type="text" name="gn_number" value="<?= htmlspecialchars($gn["gn_number"]) ?>" required>
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Population</label>
              <input type="number" name="population" value="<?= htmlspecialchars($gn["population"]) ?>">
            </div>

            <div>
              <label>Households</label>
              <input type="number" name="households_count" value="<?= htmlspecialchars($gn["households_count"]) ?>">
            </div>
          </div>

          <div class="two-col">
            <div>
              <label>Phone</label>
              <input type="text" name="contact_phone" value="<?= htmlspecialchars($gn["contact_phone"]) ?>">
            </div>

            <div>
              <label>Email</label>
              <input type="email" name="contact_email" value="<?= htmlspecialchars($gn["contact_email"]) ?>">
            </div>
          </div>

          <label>Address</label>
          <textarea name="address" rows="4"><?= htmlspecialchars($gn["address"]) ?></textarea>

          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
            <button type="submit" class="btn-update">Update Profile</button>
            <a class="btn red" href="view.php">Cancel</a>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
</body>
</html>