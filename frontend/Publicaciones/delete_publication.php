<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (!$id) {
        throw new Exception('ID no proporcionado');
    }

    $result = $supabase->deletePublication($id);
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al eliminar la publicación');
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>