<?php

// =============================
// CONEXIÓN MARIADB
// =============================

$host = "localhost";
$user = "slider_user";
$pass = "TuPassword123!";
$db   = "slider_app";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {

    die("Error en conexión con MariaDB");

}


// =============================
// CONEXIÓN POSTGRESQL
// =============================

$pgconn = @pg_connect("
    host=localhost
    dbname=slider_app_pg
    user=slider_user_pg
    password=TuPassword123!
");

?>
