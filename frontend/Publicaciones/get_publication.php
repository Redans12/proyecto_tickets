<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID no proporcionado');
    }
    
    $id = $_GET['id'];
    $result = $supabase->getPublication($id);
    
    if ($result['status'] >= 200 && $result['status'] < 300 && !empty($result['data'])) {
        echo json_encode($result['data'][0]);
    } else {
        throw new Exception('Publicación no encontrada');
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>