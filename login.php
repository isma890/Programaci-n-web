<?php

header('Content-Type: application/json');

session_start();

include 'conexion.php';

if(
    !isset($_POST['correo']) ||
    !isset($_POST['password'])
){

    echo json_encode([
        "status" => "error",
        "mensaje" => "Faltan datos"
    ]);

    exit;
}

$correo = trim($_POST['correo']);
$password = trim($_POST['password']);

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");

$stmt->bind_param("s", $correo);

$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows > 0){

    $usuario = $resultado->fetch_assoc();

    if(password_verify($password, $usuario['password'])){

        $_SESSION['usuario'] = $usuario['correo'];

        echo json_encode([
            "status" => "ok",
            "mensaje" => "Bienvenido"
        ]);

    }else{

        echo json_encode([
            "status" => "error",
            "mensaje" => "Contraseña incorrecta"
        ]);

    }

}else{

    echo json_encode([
        "status" => "error",
        "mensaje" => "Usuario no encontrado"
    ]);

}

?>