<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    // Puede recibir un ID combinado o IDs separados
    $id_relacion = isset($_GET['id']) ? $_GET['id'] : null;
    $id_publicacion = isset($_GET['id_publicacion']) ? $_GET['id_publicacion'] : null;
    $id_ticket = isset($_GET['id_ticket']) ? $_GET['id_ticket'] : null;
    
    // Si recibimos un ID combinado, extraemos los IDs individuales
    if ($id_relacion && !$id_publicacion && !$id_ticket) {
        $parts = explode('-', $id_relacion);
        if (count($parts) === 2) {
            $id_publicacion = $parts[0];
            $id_ticket = $parts[1];
        }
    }
    
    if (!$id_publicacion || !$id_ticket) {
        throw new Exception('IDs no proporcionados correctamente');
    }

    $result = $supabase->deletePublicationTicket($id_publicacion, $id_ticket);
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al eliminar la relación');
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>