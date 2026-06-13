<?php
require_once("../config/db.php");
if (session_status() === PHP_SESSION_NONE) session_start();

$username = trim($_POST["username"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($username === "" || $password === "") {
  header("Location: login.php?error=1");
  exit;
}


$stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
if (!$stmt) die("Prepare failed: " . $conn->error);

$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
  header("Location: login.php?error=1");
  exit;
}


if (isset($user["is_active"]) && (int)$user["is_active"] !== 1) {
  header("Location: login.php?error=1");
  exit;
}
if (isset($user["status"]) && strtoupper((string)$user["status"]) !== "ACTIVE") {
  header("Location: login.php?error=1");
  exit;
}


$ok = false;

if (isset($user["password_hash"]) && $user["password_hash"] !== "") {
  $ok = password_verify($password, $user["password_hash"]);
} elseif (isset($user["password"]) && $user["password"] !== "") {

  $ok = hash_equals((string)$user["password"], $password);
}

if (!$ok) {
  header("Location: login.php?error=1");
  exit;
}


$_SESSION["user_id"]  = (int)$user["user_id"];
$_SESSION["role"]     = $user["role"] ?? "COMMUNITY";
$_SESSION["gn_id"]    = $user["gn_id"] ?? null;
$_SESSION["full_name"] = $user["full_name"] ?? "";

header("Location: ../dashboard/index.php");
exit;