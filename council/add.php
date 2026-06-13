<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER", "COUNCIL"]);
require_once("../config/db.php");

$gn_id = $_SESSION["gn_id"] ?? 1;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $role_title = $_POST["role_title"];
    $nic = $_POST["nic"];
    $phone = $_POST["phone"];
    $joined_date = $_POST["joined_date"];

    $stmt = $conn->prepare("INSERT INTO council_members 
        (gn_id, name, role_title, nic, phone, joined_date) 
        VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("isssss", $gn_id, $name, $role_title, $nic, $phone, $joined_date);
    $stmt->execute();

    header("Location: list.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add Council Member</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
  <div class="shell">
    <div class="wrapper" style="justify-content:center;">

      <div class="content" style="max-width:600px;">

        <div class="topbar">
          <div><b>Add Council Member</b></div>
          <div class="role"><?= htmlspecialchars($_SESSION["role"]) ?></div>
        </div>

        <div class="card">

          <a class="btn dark" href="list.php">⬅ Back to List</a>

          <form method="POST" style="margin-top:20px;">

            <label>Full Name</label>
            <input type="text" name="name" required>

            <label>Role / Position</label>
            <input type="text" name="role_title" required>

            <label>NIC</label>
            <input type="text" name="nic" required>

            <label>Phone</label>
            <input type="text" name="phone" required>

            <label>Joined Date</label>
            <input type="date" name="joined_date" required>

            <button type="submit" class="btn-save" style="margin-top:10px;">Save Member</button>

          </form>

        </div>

      </div>
    </div>
  </div>
</body>
</html>
