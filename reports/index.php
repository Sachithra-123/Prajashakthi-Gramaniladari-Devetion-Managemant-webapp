<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER","DIV_SECRETARIAT","COUNCIL","COMMUNITY"]);
require_once("../config/db.php");

$gn_id = $_SESSION["gn_id"] ?? 1;
$role  = $_SESSION["role"] ?? "";


$totalBeneficiaries = 0;
$eligible = 0;
$notEligible = 0;
$pending = 0;

if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM beneficiaries WHERE gn_id=?")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $totalBeneficiaries = (int)$stmt->get_result()->fetch_assoc()["c"];
}
if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM beneficiaries WHERE gn_id=? AND status='Eligible'")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $eligible = (int)$stmt->get_result()->fetch_assoc()["c"];
}
if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM beneficiaries WHERE gn_id=? AND status='Not Eligible'")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $notEligible = (int)$stmt->get_result()->fetch_assoc()["c"];
}
if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM beneficiaries WHERE gn_id=? AND status='Pending'")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $pending = (int)$stmt->get_result()->fetch_assoc()["c"];
}


$planned = 0; $ongoing = 0; $completed = 0;

if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM projects WHERE gn_id=? AND status='Planned'")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $planned = (int)$stmt->get_result()->fetch_assoc()["c"];
}
if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM projects WHERE gn_id=? AND status='Ongoing'")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $ongoing = (int)$stmt->get_result()->fetch_assoc()["c"];
}
if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM projects WHERE gn_id=? AND status='Completed'")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $completed = (int)$stmt->get_result()->fetch_assoc()["c"];
}


$totalNeeds = 0;
$totalMeetings = 0;

if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM community_needs WHERE gn_id=?")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $totalNeeds = (int)$stmt->get_result()->fetch_assoc()["c"];
}
if ($stmt = $conn->prepare("SELECT COUNT(*) c FROM meetings WHERE gn_id=?")) {
  $stmt->bind_param("i",$gn_id); $stmt->execute();
  $totalMeetings = (int)$stmt->get_result()->fetch_assoc()["c"];
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>GN Division Summary Report</title>
  <link rel="stylesheet" href="../assets/style.css">


  <style>
    .report-title{font-size:30px;margin:0;color:#0b1c2d;}
    .report-sub{margin-top:6px;color:#556;line-height:1.5;}
    .section-title{margin:22px 0 10px 0;color:#0b1c2d;font-size:22px;}
    .stat p{margin-top:8px;}
    .actions-row{
      display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center;
      margin-bottom:14px;
    }
    .pill{
      display:inline-block;padding:4px 10px;border-radius:999px;
      font-size:12px;font-weight:800;background:#0b1c2d;color:#f1c40f;
    }
    @media print{
      body{background:#fff !important;}
      .sidebar, .logout, .actions-row, .topbar {display:none !important;}
      .shell{margin:0 !important;}
      .card{box-shadow:none !important;}
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
        <a href="../dashboard/index.php">Dashboard</a>
        <a href="../beneficiaries/list.php">Beneficiaries</a>
        <a href="../projects/list.php">Projects</a>
        <a href="../needs/list.php">Needs</a>
        <a href="../meetings/list.php">Meetings</a>
        <a class="active" href="index.php">Reports</a>
      </div>

      <a class="logout" href="../auth/logout.php">Logout</a>
    </div>

   
    <div class="content">

      <div class="topbar">
        <div><b>GN Division Summary Report</b> <span class="pill"><?= htmlspecialchars($role) ?></span></div>
        <div class="role">GN ID: <?= (int)$gn_id ?></div>
      </div>

      <div class="card">

        
        <div class="actions-row">
          <div>
            <h1 class="report-title">GN Division Summary Report</h1>
            <div class="report-sub">
              This report shows overall statistics of Praja Shakthi GN Division activities.
            </div>
          </div>

          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a class="btn dark" href="../dashboard/index.php">⬅ Back to Dashboard</a>
            <button class="btn" onclick="window.print()">Print</button>
          </div>
        </div>

     
        <div class="section-title">Beneficiaries</div>
        <div class="grid">
          <div class="stat"><h2><?= $totalBeneficiaries ?></h2><p>Total Beneficiaries</p></div>
          <div class="stat"><h2><?= $eligible ?></h2><p>Eligible</p></div>
          <div class="stat"><h2><?= $notEligible ?></h2><p>Not Eligible</p></div>
          <div class="stat"><h2><?= $pending ?></h2><p>Pending</p></div>
        </div>

       
        <div class="section-title">Projects</div>
        <div class="grid">
          <div class="stat"><h2><?= $planned ?></h2><p>Planned</p></div>
          <div class="stat"><h2><?= $ongoing ?></h2><p>Ongoing</p></div>
          <div class="stat"><h2><?= $completed ?></h2><p>Completed</p></div>
        </div>

      
        <div class="section-title">Other Activities</div>
        <div class="grid">
          <div class="stat"><h2><?= $totalNeeds ?></h2><p>Total Community Needs</p></div>
          <div class="stat"><h2><?= $totalMeetings ?></h2><p>Total Meetings</p></div>
        </div>

      </div>
    </div>

  </div>
</div>
</body>
</html>
