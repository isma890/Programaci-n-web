<?php

header('Content-Type: application/json');

include "conexion.php";

if(!isset($_FILES['imagen'])){

    echo json_encode([
        "status" => "error",
        "mensaje" => "No se recibió imagen"
    ]);

    exit;
}

if(!isset($_POST['nombre'])){

    echo json_encode([
        "status" => "error",
        "mensaje" => "Falta nombre"
    ]);

    exit;
}

$nombre = trim($_POST['nombre']);

if($nombre == ""){

    echo json_encode([
        "status" => "error",
        "mensaje" => "Nombre vacío"
    ]);

    exit;
}

$archivo = $_FILES['imagen'];

if($archivo['error'] !== 0){

    echo json_encode([
        "status" => "error",
        "mensaje" => "Error al subir archivo"
    ]);

    exit;
}

$carpeta = "uploads/";

if(!file_exists($carpeta)){

    mkdir($carpeta, 0777, true);
}

$nombreArchivo = time() . "_" . basename($archivo['name']);

$ruta = $carpeta . $nombreArchivo;

if(move_uploaded_file($archivo['tmp_name'], $ruta)){

    // =========================
    // GUARDAR EN MARIADB
    // =========================

    $stmt = $conn->prepare("
        INSERT INTO imagenes(nombre, ruta)
        VALUES(?, ?)
    ");

    $stmt->bind_param("ss", $nombre, $ruta);

    $stmt->execute();


    // =========================
    // GUARDAR EN POSTGRESQL
    // =========================

    pg_query_params(
        $pgconn,
        "INSERT INTO imagenes(nombre, ruta)
        VALUES($1, $2)",
        array($nombre, $ruta)
    );

    echo json_encode([
        "status" => "ok",
        "mensaje" => "Imagen subida correctamente",
        "ruta" => $ruta
    ]);

}else{

    echo json_encode([
        "status" => "error",
        "mensaje" => "Error al mover archivo"
    ]);

}

?>
