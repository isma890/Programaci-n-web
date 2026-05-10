<?php

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conexion.php';


if(
    !isset($_POST['nombre']) ||
    !isset($_POST['correo']) ||
    !isset($_POST['password'])
){
    echo json_encode([
        "status" => "error",
        "mensaje" => "Faltan datos"
    ]);
    exit;
}

$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$password = trim($_POST['password']);


if($nombre == "" || $correo == "" || $password == ""){
    echo json_encode([
        "status" => "error",
        "mensaje" => "Completa todos los campos"
    ]);
    exit;
}


if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
    echo json_encode([
        "status" => "error",
        "mensaje" => "Correo inválido"
    ]);
    exit;
}


if(
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[\W]/', $password)
){
    echo json_encode([
        "status" => "error",
        "mensaje" => "Contraseña insegura"
    ]);
    exit;
}


/* =========================
   VERIFICAR EN MARIADB
========================= */

$check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
$check->bind_param("s", $correo);
$check->execute();

$resultado = $check->get_result();

if($resultado->num_rows > 0){
    echo json_encode([
        "status" => "error",
        "mensaje" => "Correo ya registrado"
    ]);
    exit;
}


/* =========================
   ENCRIPTAR PASSWORD
========================= */

$passwordHash = password_hash($password, PASSWORD_DEFAULT);


/* =========================
   INSERTAR EN MARIADB
========================= */

$stmt = $conn->prepare("
    INSERT INTO usuarios(nombre, correo, password)
    VALUES(?,?,?)
");

$stmt->bind_param("sss", $nombre, $correo, $passwordHash);

if(!$stmt->execute()){
    echo json_encode([
        "status" => "error",
        "mensaje" => "Error MariaDB"
    ]);
    exit;
}


/* =========================
   INSERTAR EN POSTGRESQL
========================= */

$pg_query = "
    INSERT INTO usuarios(nombre, correo, password)
    VALUES($1, $2, $3)
";

$pg_result = pg_query_params(
    $pg_conn,
    $pg_query,
    array($nombre, $correo, $passwordHash)
);

if(!$pg_result){

    echo json_encode([
        "status" => "error",
        "mensaje" => pg_last_error($pg_conn)
    ]);

    exit;
}


/* =========================
   TODO CORRECTO
========================= */

echo json_encode([
    "status" => "ok",
    "mensaje" => "Registro exitoso"
]);

?>
