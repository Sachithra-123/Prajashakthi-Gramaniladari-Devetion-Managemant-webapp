<?php
require_once("../config/guard.php");
allow_roles(["GN_OFFICER"]);
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once("../config/db.php");

$id = intval($_POST["gn_id"]);

$gn_name = $_POST["gn_name"];
$gn_number = $_POST["gn_number"];
$population = intval($_POST["population"]);
$households = intval($_POST["households_count"]);
$phone = $_POST["contact_phone"];
$email = $_POST["contact_email"];
$address = $_POST["address"];

$stmt = $conn->prepare("
    UPDATE gn_division SET 
        gn_name = ?, 
        gn_number = ?, 
        population = ?, 
        households_count = ?, 
        contact_phone = ?, 
        contact_email = ?, 
        address = ?
    WHERE gn_id = ?
");

$stmt->bind_param(
    "ssissssi",
    $gn_name,
    $gn_number,
    $population,
    $households,
    $phone,
    $email,
    $address,
    $id
);

$stmt->execute();

header("Location: view.php");
exit;
