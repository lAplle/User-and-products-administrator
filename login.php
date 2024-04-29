<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require __DIR__ . "/database.php";

    $sql = sprintf("SELECT * FROM user 
                    WHERE email = '%s'",
                    $mysqli->real_escape_string($_POST["email"]));

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($_POST["password"], $user["password_hash"])) {
            if ($user["status"] == 'rejected') {
                echo "Su solicitud de registro ha sido rechazada. Por favor, póngase en contacto con el administrador para obtener más información.";
                exit;
            } elseif ($user["status"] == 'pending') {
                echo "Su solicitud no ha sido aceptada todavía. Por favor, póngase en contacto con el administrador para obtener más información.";
                exit;
            }

            session_start();
            $_SESSION["user_id"] = $user["id"];
            if ($user["is_admin"] == 1) {
                $_SESSION["is_admin"] = 1;
                header("Location: admin.php");
            } else {
                header("Location: user.php");
            }
            exit;
        } else {
            echo "Credenciales incorrectas";
        }
    } else {
        echo "Credenciales incorrectas";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Login</h1>

    <form method="post">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button>Log in</button>
    </form>
    <p>No tienes una cuenta? Puedes registrarte haciendo click <a href="index.html">aquí</a>.</p>
</body>
</html>