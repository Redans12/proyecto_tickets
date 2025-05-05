<?php
require_once 'config/Database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexión a Supabase exitosa',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $e->getMessage()
    ]);
}
?>