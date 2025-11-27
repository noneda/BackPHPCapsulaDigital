<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ====== CONEXIÓN ======
$conexion = new mysqli(
    "localhost",
    "u571423134_rootc",
    "Cota2025**",
    "u571423134_capsula"
);

if ($conexion->connect_error) {
    die("Error conexión BD: " . $conexion->connect_error);
}

// ====== DATOS DEL FORMULARIO ======
$nombre  = $_POST['nombre'] ?? '';
$tipo    = $_POST['tipo_aporte'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

$archivo_nombre = "";

// ====== MANEJO DE ARCHIVO ======
$carpeta = __DIR__ . "/capsula2025/";

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

if (!empty($_FILES['archivo']['name'])) {

    $archivo_original = basename($_FILES["archivo"]["name"]);
    $archivo_nombre = time() . "_" . $archivo_original;
    $ruta = $carpeta . $archivo_nombre;

    if (!move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta)) {
        die("Error subiendo archivo. Verifica permisos de la carpeta capsula2025.");
    }
}

// ====== INSERTANDO EN LA BASE DE DATOS ======
$sql = $conexion->prepare("
    INSERT INTO reportes (nombre, tipo_aporte, mensaje, archivo)
    VALUES (?, ?, ?, ?)
");

$sql->bind_param("ssss", $nombre, $tipo, $mensaje, $archivo_nombre);

if ($sql->execute()) {
    echo "<h2>¡Reporte guardado correctamente!</h2>";
    echo "<a href='index.php'>Volver</a>";
} else {
    echo "Error al guardar: " . $sql->error;
}

$conexion->close();
?>
