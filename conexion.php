<?php

/* ==========
   MARIADB
========== */

$host = "localhost";
$user = "slider_user";
$pass = "TuPassword123!";
$db   = "slider_app";

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error){
    die("Error MariaDB: " . $conn->connect_error);
}


/* ==========
   POSTGRESQL
========== */

$pg_conn = pg_connect("
    host=localhost
    dbname=slider_app_pg
    user=slider_user_pg
    password=TuPassword123!
");

if(!$pg_conn){
    die("Error PostgreSQL");
}

?>
