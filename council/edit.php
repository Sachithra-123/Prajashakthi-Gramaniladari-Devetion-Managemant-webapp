<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER", "COUNCIL"]);
require_once("../config/db.php");

$id = $_GET["id"] ?? 0;

$stmt = $conn->prepare("SELECT * FROM council_members WHERE member_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
    die("Council member not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $role_title = $_POST["role_title"];
    $nic = $_POST["nic"];
    $phone = $_POST["phone"];
    $joined_date = $_POST["joined_date"];

    $stmt = $conn->prepare("UPDATE council_members 
        SET name=?, role_title=?, nic=?, phone=?, joined_date=? 
        WHERE member_id=?");
    $stmt->bind_param("sssssi", $name, $role_title, $nic, $phone, $joined_date, $id);
    $stmt->execute();

    header("Location: list.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Council Member</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
  <div class="shell">
    <div class="wrapper" style="justify-content:center;">

      <div class="content" style="max-width:600px;">

        <div class="topbar">
          <div><b>Edit Council Member</b></div>
          <div class="role"><?= htmlspecialchars($_SESSION["role"]) ?></div>
        </div>

        <div class="card">

          <a class="btn dark" href="list.php">⬅ Back to List</a>

          <form method="POST" style="margin-top:20px;">

            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($row["name"]) ?>" required>

            <label>Role / Position</label>
            <input type="text" name="role_title" value="<?= htmlspecialchars($row["role_title"]) ?>" required>

            <label>NIC</label>
            <input type="text" name="nic" value="<?= htmlspecialchars($row["nic"]) ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($row["phone"]) ?>" required>

            <label>Joined Date</label>
            <input type="date" name="joined_date" value="<?= htmlspecialchars($row["joined_date"]) ?>" required>

           <button type="submit" class="btn-update" style="margin-top:10px;">Update Member</button>

          </form>

        </div>

      </div>
    </div>
  </div>
</body>
</html>
