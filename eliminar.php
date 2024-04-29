<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['eliminar'])) {
    $mysqli = require __DIR__ . "/database.php";
    $id_usuario = $_POST['id'];

    $sql = "DELETE FROM user WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}
?>
