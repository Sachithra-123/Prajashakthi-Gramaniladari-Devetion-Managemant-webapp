<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]);
if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }
require_once("../config/db.php");

$id = intval($_GET["id"] ?? 0);
$gn_id = $_SESSION["gn_id"] ?? 1;

$stmt = $conn->prepare("DELETE FROM beneficiaries WHERE beneficiary_id=? AND gn_id=?");
$stmt->bind_param("ii", $id, $gn_id);
$stmt->execute();

header("Location: list.php");
exit;
