<?php

function settingsCors() {
    // $allowedOrigins = [
    //     'https://www.cota-cundinamarca.gov.co',
    //     'http://localhost:8000'
    // ];
    
    // $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    header('Content-Type: application/json; charset=utf-8');
    
    // if (in_array($origin, $allowedOrigins)) {
    //     header("Access-Control-Allow-Origin: $origin");
    //     header('Access-Control-Allow-Credentials: true');
    // }

    header('Access-Control-Allow-Origin: *');


    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}


function res($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}


function validateJSONData($data, $required) {
    $missing = [];
    
    foreach ($required as $need) {
        if (!isset($data[$need]) || empty($data[$need])) {
            $missing[] = $need;
        }
    }
    
    if (!empty($missing)) {
        throw new Exception('Faltan campos requeridos: ' . implode(', ', $missing));
    }
    
    return true;
}


function validateFile($file, $maxSizeMB = 25) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('No se envió ningún archivo');
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errores = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño del formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
            UPLOAD_ERR_EXTENSION => 'Extensión de PHP detuvo la carga'
        ];
        throw new Exception($errores[$file['error']] ?? 'Error desconocido al subir');
    }
    
    $maxSize = $maxSizeMB * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new Exception("El archivo no debe superar {$maxSizeMB}MB");
    }
    
    $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $allowedMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png'
    ];
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception("Extensión no permitida. Solo: " . implode(', ', $allowedExtensions));
    }
    
    if (!in_array($mimeType, $allowedMimes)) {
        throw new Exception("Tipo de archivo no válido");
    }
    
    return [
        'extension' => $extension,
        'mime_type' => $mimeType
    ];
}

function saveFile($file, $extension) {
    $uploadDir = __DIR__ . '/capsula2025/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uniqueName = uniqid('doc_', true) . '_' . time() . '.' . $extension;
    $destination = $uploadDir . $uniqueName;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Error al guardar el archivo');
    }
    
    return [
        'filename' => $uniqueName,
        'path' => $destination,
        'size' => $file['size']
    ];
}
