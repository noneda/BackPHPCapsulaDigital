<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u571423134_capsula');
define('DB_USER', 'u571423134_rootc');
define('DB_PASS', 'Cota2025**');

// Connect to Base Data
function connectDB() {
    try { 
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
            PDO::ATTR_EMULATE_PREPARES => false 
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        
        return $pdo;
        
    } catch (PDOException $e) {
        throw new Exception("Error de conexión: " . $e->getMessage());
    }
}

// Make a Save
function save($data, $file) {
    $db = connectDB();
    
    try {
        $sql = "INSERT INTO reportes (
                    nombre, 
                    tipo_aporte, 
                    mensaje, 
                    archivo,
                ) VALUES (
                    :name, 
                    :type, 
                    :message, 
                    :filename,
                )";
        
        $stmt = $db->prepare($sql);
        
        $stmt->execute([
            ':name' => $data['name'],
            ':type' => $data['type'],
            ':message' => $data['message'],
            ':filename' => $file
        ]);
        
    } catch (PDOException $e) {
        throw new Exception("Error al guardar en DB: " . $e->getMessage());
    } finally {
        $db = null;
    }
}
