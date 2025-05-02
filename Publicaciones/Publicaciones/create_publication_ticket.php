<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['id_publicacion']) || !isset($_POST['id_ticket'])) {
        throw new Exception('Faltan datos requeridos');
    }
    
    $id_publicacion = $_POST['id_publicacion'];
    $id_ticket = $_POST['id_ticket'];
    
    // Verificar si la relación ya existe
    $checkResult = $supabase->getPublicationTicket($id_publicacion, $id_ticket);
    
    if ($checkResult['status'] >= 200 && $checkResult['status'] < 300 && !empty($checkResult['data'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Esta relación ya existe'
        ]);
        exit;
    }
    
    // Crear la nueva relación
    $data = [
        'id_publicacion' => $id_publicacion,
        'id_ticket' => $id_ticket
    ];
    
    $result = $supabase->createPublicationTicket($data);
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        echo json_encode([
            'success' => true,
            'message' => 'Relación creada correctamente'
        ]);
    } else {
        throw new Exception('Error al crear la relación');
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>