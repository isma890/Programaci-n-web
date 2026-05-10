<?php
include "conexion.php";

$id = $_POST['id'];

$res = $conn->query("SELECT ruta FROM imagenes WHERE id=$id");
$row = $res->fetch_assoc();

if($row){
    if(file_exists($row['ruta'])){
        unlink($row['ruta']);
    }
}

$conn->query("DELETE FROM imagenes WHERE id=$id");

echo json_encode(["mensaje" => "Eliminado"]);