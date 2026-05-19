<?php

include "conexion.php";

header("Content-Type: application/json");

$actual = isset($_GET['actual'])
    ? intval($_GET['actual'])
    : 0;

$direccion = isset($_GET['direccion'])
    ? $_GET['direccion']
    : 'primera';


// 🔥 PRIMERA IMAGEN
if($direccion == "primera"){

    $sql = "SELECT * FROM imagenes
            ORDER BY id ASC
            LIMIT 1";
}


// 🔥 SIGUIENTE IMAGEN
elseif($direccion == "siguiente"){

    $sql = "SELECT * FROM imagenes
            WHERE id > $actual
            ORDER BY id ASC
            LIMIT 1";

    $res = $conn->query($sql);

    // 🔥 SI YA NO HAY MÁS
    if($res->num_rows == 0){

        $sql = "SELECT * FROM imagenes
                ORDER BY id ASC
                LIMIT 1";
    }
}


// 🔥 IMAGEN ANTERIOR
elseif($direccion == "anterior"){

    $sql = "SELECT * FROM imagenes
            WHERE id < $actual
            ORDER BY id DESC
            LIMIT 1";

    $res = $conn->query($sql);

    // 🔥 SI YA NO HAY MÁS
    if($res->num_rows == 0){

        $sql = "SELECT * FROM imagenes
                ORDER BY id DESC
                LIMIT 1";
    }
}


// 🔥 VER IMAGEN ESPECÍFICA
elseif($direccion == "ver"){

    $sql = "SELECT * FROM imagenes
            WHERE id = $actual
            LIMIT 1";
}


// 🔥 EJECUTAR
$res = $conn->query($sql);

$data = [];

while($row = $res->fetch_assoc()){

    $data[] = $row;

}

echo json_encode($data);

?>
