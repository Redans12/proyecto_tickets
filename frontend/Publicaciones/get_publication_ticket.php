<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    // Obtener los IDs de la URL
    $id_publicacion = isset($_GET['id_publicacion']) ? $_GET['id_publicacion'] : null;
    $id_ticket = isset($_GET['id_ticket']) ? $_GET['id_ticket'] : null;
    
    if (!$id_publicacion && !$id_ticket) {
        // Si es un ID combinado, separarlo
        $id_relacion = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id_relacion) {
            $parts = explode('-', $id_relacion);
            if (count($parts) === 2) {
                $id_publicacion = $parts[0];
                $id_ticket = $parts[1];
            }
        }
    }
    
    if (!$id_publicacion || !$id_ticket) {
        throw new Exception('Se requieren los IDs de publicación y ticket');
    }

    $result = $supabase->getPublicationTicket($id_publicacion, $id_ticket);
    
    if ($result['status'] >= 200 && $result['status'] < 300 && !empty($result['data'])) {
        $relation = $result['data'][0];
        echo json_encode([
            'success' => true,
            'data' => [
                'id_relacion' => $relation['id_publicacion'] . '-' . $relation['id_ticket'],
                'id_publicacion' => $relation['id_publicacion'],
                'id_ticket' => $relation['id_ticket'],
                'plataforma' => $relation['publicaciones']['plataforma'],
                'enlace' => $relation['publicaciones']['enlace']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Relación no encontrada'
        ]);
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>