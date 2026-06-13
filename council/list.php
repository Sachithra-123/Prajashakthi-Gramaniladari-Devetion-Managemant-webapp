<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER", "COUNCIL"]);

require_once("../config/db.php");

$gn_id = $_SESSION["gn_id"] ?? 1;
$q = trim($_GET["q"] ?? "");

if ($q !== "") {
    $stmt = $conn->prepare("SELECT * FROM council_members 
                            WHERE gn_id=? AND name LIKE CONCAT('%', ?, '%')
                            ORDER BY member_id DESC");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("is", $gn_id, $q);
} else {
    $stmt = $conn->prepare("SELECT * FROM council_members 
                            WHERE gn_id=? 
                            ORDER BY member_id DESC");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("i", $gn_id);
}

$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Council Members</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
  <div class="shell">
    <div class="wrapper" style="justify-content:center;">

      <div class="content" style="max-width:1000px;">

        <div class="topbar">
          <div><b>Council Management</b></div>
          <div class="role"><?= htmlspecialchars($_SESSION["role"] ?? "") ?></div>
        </div>

        <div class="card">

          
          <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div style="display:flex;flex-wrap:wrap;gap:10px;">
              <a class="btn dark" href="../dashboard/index.php">⬅ Dashboard</a>
              <a class="btn green" href="add.php">+ Add Council Member</a>
            </div>

            <!-- Search -->
            <form method="GET" style="display:flex;gap:10px;align-items:center;">
              <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by name..." style="max-width:260px;">
              <button type="submit" class="btn-back" href="list.php" >Search</button>
            </form>
          </div>

          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th>NIC</th>
                <th>Phone</th>
                <th>Joined</th>
                <th style="width:180px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($res->num_rows === 0): ?>
                <tr><td colspan="6">No council members found.</td></tr>
              <?php else: ?>
                <?php while($row = $res->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["role_title"]) ?></td>
                    <td><?= htmlspecialchars($row["nic"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td><?= htmlspecialchars($row["joined_date"]) ?></td>
                    <td>
                      <a class="btn" href="edit.php?id=<?= (int)$row["member_id"] ?>">Edit</a>
                      <a class="btn red" href="delete.php?id=<?= (int)$row["member_id"] ?>"
                         onclick="return confirm('Delete this member?')">Delete</a>
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
