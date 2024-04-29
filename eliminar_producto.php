<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php"); 
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $producto_id = $_POST['id'];

    $mysqli = require __DIR__ . "/database.php";

    $sql_eliminar_compras = "DELETE FROM compras WHERE id_producto = ?";
    $stmt_eliminar_compras = $mysqli->prepare($sql_eliminar_compras);
    $stmt_eliminar_compras->bind_param("i", $producto_id);
    $stmt_eliminar_compras->execute();

    $sql_eliminar_producto = "DELETE FROM productos WHERE id = ?";
    $stmt_eliminar_producto = $mysqli->prepare($sql_eliminar_producto);
    $stmt_eliminar_producto->bind_param("i", $producto_id);

    if ($stmt_eliminar_producto->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        die("Error al eliminar el producto");
    }
} else {
    header("Location: admin.php");
    exit;
}
?>
