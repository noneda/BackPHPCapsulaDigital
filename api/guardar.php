<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Mostrar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === CONEXIÓN A LA BD ===
$conexion = new mysqli(
    "localhost",
    "u571423134_rootc",
    "Cota2025**",
    "u571423134_capsula"
);

if ($conexion->connect_error) {
    echo json_encode(["error" => "Error conexión BD"]);
    exit;
}

// =========================
// DATOS RECIBIDOS (POST)
// =========================
$nombre  = $_POST['nombre'] ?? null;
$tipo    = $_POST['tipo_aporte'] ?? null;
$mensaje = $_POST['mensaje'] ?? null;

if (!$nombre || !$tipo || !$mensaje) {
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

// =========================
// MANEJO DE ARCHIVO
// =========================
$archivo_guardado = "";
$carpeta = __DIR__ . "/../capsula2025/";

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {

    $original = basename($_FILES["archivo"]["name"]);
    $archivo_guardado = time() . "_" . $original;
    $ruta = $carpeta . $archivo_guardado;

    if (!move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta)) {
        echo json_encode(["error" => "No se pudo guardar el archivo"]);
        exit;
    }
}

// =========================
// GUARDAR EN BD
// =========================
$sql = $conexion->prepare("
    INSERT INTO reportes (nombre, tipo_aporte, mensaje, archivo)
    VALUES (?, ?, ?, ?)
");
$sql->bind_param("ssss", $nombre, $tipo, $mensaje, $archivo_guardado);

if ($sql->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Reporte guardado",
        "id" => $conexion->insert_id
    ]);
} else {
    echo json_encode(["error" => $sql->error]);
}

$conexion->close();
?>
