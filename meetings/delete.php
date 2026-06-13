<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user_id"])) { 
    header("Location: ../auth/login.php"); 
    exit; 
}

require_once("../config/db.php");

$gn_id = $_SESSION["gn_id"] ?? 1;
$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Invalid meeting id");
}


$delMin = $conn->prepare("DELETE FROM meeting_minutes WHERE meeting_id=?");
$delMin->bind_param("i", $id);
$delMin->execute();


$stmt = $conn->prepare("DELETE FROM meetings WHERE meeting_id=? AND gn_id=?");
$stmt->bind_param("ii", $id, $gn_id);
$stmt->execute();

if ($stmt->affected_rows <= 0) {
    echo "Delete failed. Meeting not found or permission issue.";
    exit;
}

header("Location: list.php");
exit;
