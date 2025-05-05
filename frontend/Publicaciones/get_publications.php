<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    $result = $supabase->getPublications();
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        echo json_encode($result['data']);
    } else {
        throw new Exception('Error al obtener las publicaciones');
    }
} catch(Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error al obtener las publicaciones: ' . $e->getMessage()
    ]);
}
?>