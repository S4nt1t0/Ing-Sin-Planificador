<?php
header('Content-Type: application/json; charset=utf-8');

$productId = isset($_GET['productId']) ? intval($_GET['productId']) : 0;
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de producto inválido', 'stock' => 0]);
    exit;
}

$link = mysqli_connect('localhost', 'santito', 'DBZczspoponp10!', 'SistemasII');
if (!$link) {
    echo json_encode(['success' => false, 'message' => 'Error en la conexión con la base de datos', 'stock' => 0]);
    exit;
}

$stmt = mysqli_prepare($link, "SELECT Stock FROM Carro WHERE Id_Carro = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor', 'stock' => 0]);
    mysqli_close($link);
    exit;
}

// enlaza el parametro de id de producto
mysqli_stmt_bind_param($stmt, 'i', $productId);
// ejecuta la consulta preparada
mysqli_stmt_execute($stmt);
// enlaza el resultado para obtener el stock
mysqli_stmt_bind_result($stmt, $stock);
// obtiene el valor del stock
$found = mysqli_stmt_fetch($stmt);
// cierra la consulta preparada
mysqli_stmt_close($stmt);

if (!$found) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado', 'stock' => 0]);
    mysqli_close($link);
    exit;
}

mysqli_close($link);
echo json_encode(['success' => true, 'stock' => intval($stock)]);
?>