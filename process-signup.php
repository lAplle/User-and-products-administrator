<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Acceso denegado");
}

define('CLAVE', '6Ldsf5spAAAAAKpGsIjUhYZpm462Cp-6n-8Y9T_O');

$token = $_POST['token'];

$cu = curl_init();
curl_setopt($cu, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($cu, CURLOPT_POST, 1);
curl_setopt($cu, CURLOPT_POSTFIELDS, http_build_query(array('secret' => CLAVE, 'response' => $token)));
curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($cu);

if ($response === false) {
    die("Error al realizar la solicitud cURL: " . curl_error($cu));
    exit("Ha ocurrido un error en el proceso. Por favor, inténtalo de nuevo más tarde.");
}

curl_close($cu);

$datos = json_decode($response, true);
$datosEncoded = json_encode($datos);

if ($datos === null) {
    die("Error al decodificar la respuesta JSON de reCAPTCHA");
}

if ($datos['success'] == 1 && $datos['score'] >= 0.5) {
    if ($datos['action'] == 'validarUsuario') {
        // $name = $_POST["name"];
        // $email = $_POST["email"];
        // $password = $_POST["password"];
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password']; 

        if (preg_match('/[<>"\'\/]+/', $password)) {
            exit("Buen intento. Suerte a la próxima.");
        }

        if (empty($name)) {
            die("El nombre es requerido.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("El email ingresado no es válido");
        }

        if (strlen($password) < 8 || !preg_match("/[a-zA-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
            die("La contraseña debe tener al menos 8 caracteres y contener al menos una letra y un número");
        }

        if ($_POST["password"] !== $_POST["password_confirmation"]) {
            die("Las contraseñas no coinciden");
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $mysqli = require __DIR__ . "/database.php";

        $sql = "INSERT INTO user (name, email, password_hash)
                VALUES (?, ?, ?)";

        $stmt = $mysqli->stmt_init();

        if (!$stmt->prepare($sql)) {
            die("Error al preparar la consulta SQL: " . $mysqli->error);
        }

        $stmt->bind_param("sss", $name, $email, $password_hash);

        try {
            if ($stmt->execute()) {
                echo '<h1>Proceso de registro</h1>
                <p id="mensaje">Registro completado. Cuando seas aceptado por un administrador, entonces podrás <a href="login.php">iniciar sesión</a>.</p>
                <p>Puedes ver los datos que devuelve la solicitud reCAPTCHA en la consola.<p/>';
            }
        } catch (mysqli_sql_exception $e) {
            if ($mysqli->errno === 1062) {
                die("El email ingresado ya está en uso");
            } else {
                die("Error al ejecutar la consulta SQL: " . $mysqli->error);
            }
        }
    }
} else {
    echo '<h1>Validación reCAPTCHA fallida</h1>
    <p>Por favor, inténtalo de nuevo más tarde.</p>
    <p>Puedes volver al registro <a href="index.html">aquí</a>.</p>';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Sign up </title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    </head>
    <body>
        <script>
            var datosEncoded = <?php echo $datosEncoded; ?>;
            console.log(datosEncoded);
        </script>
    </body>
</html>
