<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $producto_id = $_GET['id'];

    $mysqli = require __DIR__ . "/database.php";

    $sql_verificar_compra = "SELECT id FROM compras WHERE id_usuario = ? AND id_producto = ?";
    $stmt_verificar_compra = $mysqli->prepare($sql_verificar_compra);
    $stmt_verificar_compra->bind_param("ii", $_SESSION['user_id'], $producto_id);
    $stmt_verificar_compra->execute();
    $result_verificar_compra = $stmt_verificar_compra->get_result();

    if ($result_verificar_compra->num_rows > 0) {
        header("Location: user.php");
        exit;
    }

    $sql_registrar_compra = "INSERT INTO compras (id_usuario, id_producto, fecha) VALUES (?, ?, NOW())";
    $stmt_registrar_compra = $mysqli->prepare($sql_registrar_compra);
    $stmt_registrar_compra->bind_param("ii", $_SESSION['user_id'], $producto_id);

    if ($stmt_registrar_compra->execute()) {
        $sql_actualizar_producto = "UPDATE productos SET id_comprador = ? WHERE id = ?";
        $stmt_actualizar_producto = $mysqli->prepare($sql_actualizar_producto);
        $stmt_actualizar_producto->bind_param("ii", $_SESSION['user_id'], $producto_id);
        $stmt_actualizar_producto->execute();

        header("Location: user.php");
        exit;
    } else {
        die("Error al realizar la compra");
    }
} else {
    header("Location: user.php");
    exit;
}
?>
