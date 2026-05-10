<?php
include "conexion.php";

$res = $conn->query("SELECT * FROM imagenes ORDER BY id DESC");

$data = [];

while($row = $res->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);