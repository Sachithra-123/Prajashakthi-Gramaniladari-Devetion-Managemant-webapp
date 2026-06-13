<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: ../auth/login.php"); exit; }
if (($_SESSION["role"] ?? "") !== "GN_OFFICER") { die("Access denied"); }

require_once("../config/db.php");

$gn_id = $_SESSION["gn_id"] ?? 1;
$id = intval($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid id");


if ($id === (int)$_SESSION["user_id"]) die("You cannot delete your own account.");

$stmt = $conn->prepare("DELETE FROM users WHERE user_id=? AND gn_id=?");
$stmt->bind_param("ii", $id, $gn_id);
$stmt->execute();

header("Location: list.php");
exit;
