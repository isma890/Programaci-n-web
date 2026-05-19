<?php

header('Content-Type: application/json');

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


// 🔥 VERIFICAR CORREO
$check = $conn->prepare(
    "SELECT id FROM usuarios WHERE correo = ?"
);

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


// 🔥 ENCRIPTAR PASSWORD
$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// 🔥 INSERTAR USUARIO
$stmt = $conn->prepare(
    "INSERT INTO usuarios(nombre, correo, password)
     VALUES(?, ?, ?)"
);

$stmt->bind_param(
    "sss",
    $nombre,
    $correo,
    $passwordHash
);


if($stmt->execute()){

    echo json_encode([
        "status" => "ok",
        "mensaje" => "Usuario registrado"
    ]);

}else{

    echo json_encode([
        "status" => "error",
        "mensaje" => "Error al registrar"
    ]);

}

?>
