<?php
require_once "methods.php";
require_once "mysql.php";

settingsCors();


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    res(['message' => 'OK']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    res([
        'error' => true,
        'message' => 'Solo se permite método POST'
    ], 405);
}


try {
    if (!isset($_POST['data']) || empty($_POST['data'])) {
        throw new Exception('No se recibieron datos JSON');
    }
    
    $jsonData = json_decode($_POST['data'], true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON inválido: ' . json_last_error_msg());
    }
    
    validateJSONData($jsonData, ['name', 'type', 'message']);
    
    $name = htmlspecialchars(trim($jsonData['name']), ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars(trim($jsonData['type']), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($jsonData['message']), ENT_QUOTES, 'UTF-8');
    
    $dataUser = [
        'name' => $name,
        'type' => $type,
        'message' => $message
    ];

    $file = 'No Send';

    if (isset($_FILES['doc']) && $_FILES['doc']['error'] !== UPLOAD_ERR_NO_FILE) {
        $infoArchivo = validateFile($_FILES['doc'], 25);
        $saveFile = saveFile($_FILES['doc'], $infoArchivo['extension']);
        $file = $saveFile['filename'];        
    }

    
    save($dataUser, $file);
    
    res([
        'error' => false,
        'message' => 'Se ha enviado tus Datos'
    ], 201);
    
} catch (Exception $e) {
    res([
        'error' => true,
        'message' => 'No se han enviado los datos',
        'details' => $e->getMessage()
    ], 400);
}
