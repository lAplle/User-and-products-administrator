<?php   

$host = "localhost";
$dbname = "id22052106_mydatabase";
$username = "id22052106_aplle";
$password = "0240MAO19ns*";


$mysqli = new mysqli(hostname: $host,
                    username: $username, 
                    password: $password, 
                    database: $dbname);


if ($mysqli->connect_errno){
    die("Connection error: ". $mysqli->connect_error);
}


return $mysqli;