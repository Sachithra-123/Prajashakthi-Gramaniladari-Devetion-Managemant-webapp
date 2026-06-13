<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]);

require_once("../config/db.php");

$msg = "";
$gn_id = $_SESSION["gn_id"] ?? 1;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $full_name = trim($_POST["full_name"] ?? "");
  $username  = trim($_POST["username"] ?? "");
  $password  = $_POST["password"] ?? "";
  $role      = $_POST["role"] ?? "COUNCIL";
  $status    = isset($_POST["status"]) ? 1 : 0;


  $allowed_roles = ["COUNCIL", "DIV_SECRETARIAT", "COMMUNITY", "GN_OFFICER"];
  if (!in_array($role, $allowed_roles, true)) {
    $msg = "Invalid role selected.";
  } elseif ($full_name === "" || $username === "" || $password === "") {
    $msg = "Please fill all required fields.";
  } else {

    $chk = $conn->prepare("SELECT user_id FROM users WHERE username=? LIMIT 1");
    if (!$chk) die("Prepare failed: " . $conn->error);
    $chk->bind_param("s", $username);
    $chk->execute();

    if ($chk->get_result()->num_rows > 0) {
      $msg = "Username already exists!";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $conn->prepare("INSERT INTO users (gn_id, full_name, username, password_hash, role, status)
                              VALUES (?, ?, ?, ?, ?, ?)");
      if (!$stmt) die("Prepare failed: " . $conn->error);

      $stmt->bind_param("issssi", $gn_id, $full_name, $username, $hash, $role, $status);
      $stmt->execute();

      header("Location: list.php");
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add User</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
  <div class="shell">
    <div class="wrapper" style="justify-content:center;">

      <div class="content" style="max-width:700px;">

        <div class="topbar">
          <div><b>Add New User</b></div>
          <div class="role"><?= htmlspecialchars($_SESSION["role"] ?? "") ?></div>
        </div>

        <div class="card">

          
          <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <a class="btn dark" href="../dashboard/index.php">⬅ Dashboard</a>
              <a class="btn" href="list.php">User List</a>
            </div>
            <span class="pill">GN ID: <?= (int)($gn_id) ?></span>
          </div>

          <?php if ($msg): ?>
            <div style="background: rgba(220,53,69,0.12); border:1px solid rgba(220,53,69,0.35); padding:12px; border-radius:12px; color:#b02a37; font-weight:700; margin-bottom:14px;">
              <?= htmlspecialchars($msg) ?>
            </div>
          <?php endif; ?>

          <form method="POST">

            <div class="form-row">
              <div>
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Eg: Nimal Perera" required>
              </div>

              <div>
                <label>Username</label>
                <input type="text" name="username" placeholder="Eg: gnuser01" required>
              </div>
            </div>

            <div class="form-row">
              <div>
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a strong password" required>
               
              </div>

              <div>
                <label>Role</label>
                <select name="role" required>
                  <option value="COUNCIL" selected>COUNCIL</option>
                  <option value="DIV_SECRETARIAT">DIV_SECRETARIAT (view-only)</option>
                  <option value="COMMUNITY">COMMUNITY (limited)</option>
                  <option value="GN_OFFICER">GN_OFFICER</option>
                </select>
                <div class="helper">Admin role is not allowed.</div>
              </div>
            </div>

            <label style="display:flex;align-items:center;gap:10px;">
              <input type="checkbox" name="status" checked style="width:auto;margin:0;">
              Active account
            </label>

            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
              <button type="submit" class="btn-save">Save User</button>
              <a class="btn red" href="list.php">Cancel</a>
            </div>

          </form>

        </div>

      </div>
    </div>
  </div>
</body>
</html>
