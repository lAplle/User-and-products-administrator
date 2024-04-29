<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

$mysqli = require __DIR__ . "/database.php";

date_default_timezone_set('America/Mexico_City');

$sql_productos_disponibles = "SELECT * FROM productos WHERE id NOT IN (SELECT id_producto FROM compras)";
$result_productos_disponibles = $mysqli->query($sql_productos_disponibles);

if (!$result_productos_disponibles) {
    die("Error al ejecutar la consulta SQL: " . $mysqli->error);
}

$sql_productos_comprados = "SELECT productos.*, compras.fecha FROM productos 
    JOIN compras ON productos.id = compras.id_producto 
    WHERE compras.id_usuario = ? AND productos.id_comprador = ?";
$stmt_productos_comprados = $mysqli->prepare($sql_productos_comprados);
$stmt_productos_comprados->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
$stmt_productos_comprados->execute();
$result_productos_comprados = $stmt_productos_comprados->get_result();

if (!$result_productos_comprados) {
    die("Error al ejecutar la consulta SQL: " . $mysqli->error);
}
$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Panel de Usuario</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    </head>
    <body>
        <h1>Bienvenido Usuario</h1>
        <a href="logout.php">Cerrar Sesión</a>
    <h2>Información de perfil:</h2>
    <p><strong>Nombre:</strong> <?php echo $usuario['name']; ?></p>
    <p><strong>Email:</strong> <?php echo $usuario['email']; ?></p>

    <h2>Productos Disponibles</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Imagen</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($producto = $result_productos_disponibles->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td><?php echo $producto['descripcion']; ?></td>
                    <td><?php echo $producto['precio']; ?></td>
                    <td><img src="<?php echo $producto['imagen']; ?>" alt="Imagen del Producto" width="100"></td>
                    <td><a href="comprar_producto.php?id=<?php echo $producto['id']; ?>">Comprar</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Productos Comprados</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Imagen</th>
                <th>Fecha de Compra</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($producto = $result_productos_comprados->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td><?php echo $producto['descripcion']; ?></td>
                    <td><?php echo $producto['precio']; ?></td>
                    <td><img src="<?php echo $producto['imagen']; ?>" alt="Imagen del Producto" width="100"></td>
                    <td><?php echo $producto['fecha']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>