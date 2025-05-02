<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['id_publicacion']) || !isset($_POST['plataforma'])) {
        throw new Exception('Datos incompletos');
    }

    $id = $_POST['id_publicacion'];
    $updateData = [
        'plataforma' => $_POST['plataforma'],
        'enlace' => isset($_POST['enlace']) ? $_POST['enlace'] : null
    ];

    $result = $supabase->updatePublication($id, $updateData);
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        echo json_encode([
            'success' => true,
            'message' => 'Publicación actualizada exitosamente'
        ]);
    } else {
        throw new Exception('Error al actualizar la publicación');
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>