<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre'], $_POST['descripcion'], $_POST['precio'], $_POST['imagen'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen = $_POST['imagen'];

    $mysqli = require __DIR__ . "/database.php";
    $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $imagen);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit;
} else {
    header("Location: admin.php");
    exit;
}
?>
