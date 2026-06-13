<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }
require_once("../config/db.php");

$gn_id = $_SESSION["gn_id"] ?? 1;
$id = intval($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid member id");

$stmt = $conn->prepare("DELETE FROM council_members WHERE member_id=? AND gn_id=?");
$stmt->bind_param("ii", $id, $gn_id);
$stmt->execute();

if ($stmt->affected_rows <= 0) {
  die("Delete failed (member not found or gn_id mismatch)");
}

header("Location: list.php");
exit;
