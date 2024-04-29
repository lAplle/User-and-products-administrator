<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id_producto = $_POST['id'];

    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $producto = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Editar Producto</title>
            <meta charset="UTF-8"> 
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
        </head>
        <body>
            <h1>Editar Producto</h1>
            <form action="guardar_edicion_producto.php" method="post">
                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $producto['nombre']; ?>"><br><br>
                <label for="descripcion">Descripción:</label><br>
                <textarea id="descripcion" name="descripcion"><?php echo $producto['descripcion']; ?></textarea><br><br>
                <label for="precio">Precio:</label>
                <input type="text" id="precio" name="precio" value="<?php echo $producto['precio']; ?>"><br><br>
                <label for="imagen">URL de la Imagen:</label>
                <input type="text" id="imagen" name="imagen" value="<?php echo $producto['imagen']; ?>"><br><br>
                <input type="submit" value="Guardar Cambios">
            </form>
        </body>
        </html>
        <?php
        exit;
    } else {
        header("Location: admin.php");
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}
?>
