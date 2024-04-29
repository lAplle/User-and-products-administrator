<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id_usuario = $_GET['id'];

$mysqli = require __DIR__ . "/database.php";

$sql = "UPDATE user SET status = 'accepted' WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->close();

header("Location: admin.php");
exit;
?>
