<?php

header('Content-Type: application/json');

include "conexion.php";


if(isset($_FILES['imagen'])){

    $nombre = trim($_POST['nombre']);
    $archivo = $_FILES['imagen'];


    /* =========================
       VALIDAR ERROR ARCHIVO
    ========================= */

    if($archivo['error'] !== 0){

        echo json_encode([
            "status" => "error",
            "mensaje" => "Error al subir archivo"
        ]);

        exit;
    }


    /* =========================
       CREAR CARPETA
    ========================= */

    if(!is_dir("uploads")){
        mkdir("uploads", 0777, true);
    }


    /* =========================
       GENERAR RUTA
    ========================= */

    $nombreArchivo = time() . "_" . basename($archivo['name']);

    $ruta = "uploads/" . $nombreArchivo;


    /* =========================
       MOVER ARCHIVO
    ========================= */

    if(move_uploaded_file($archivo['tmp_name'], $ruta)){


        /* =========================
           GUARDAR EN MARIADB
        ========================= */

        $stmt = $conn->prepare("
            INSERT INTO imagenes(nombre, ruta)
            VALUES(?, ?)
        ");

        $stmt->bind_param("ss", $nombre, $ruta);

        if(!$stmt->execute()){

            echo json_encode([
                "status" => "error",
                "mensaje" => "Error MariaDB"
            ]);

            exit;
        }


        /* =========================
           GUARDAR EN POSTGRESQL
        ========================= */

        $pg_query = "
            INSERT INTO imagenes(nombre, ruta)
            VALUES($1, $2)
        ";

        $pg_result = pg_query_params(
            $pg_conn,
            $pg_query,
            array($nombre, $ruta)
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
            "mensaje" => "Imagen subida correctamente"
        ]);

    }else{

        echo json_encode([
            "status" => "error",
            "mensaje" => "Error al mover archivo"
        ]);
    }

}else{

    echo json_encode([
        "status" => "error",
        "mensaje" => "No se recibió imagen"
    ]);
}

?>
