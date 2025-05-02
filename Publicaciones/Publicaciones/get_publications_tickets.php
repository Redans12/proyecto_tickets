<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    $result = $supabase->getPublicationsTickets();
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        // Formatear los datos para la tabla como en tu versión original
        $formattedRelations = array_map(function($rel) {
            return [
                'id_relacion' => $rel['id_publicacion'] . '-' . $rel['id_ticket'],
                'publicacion' => $rel['publicaciones']['plataforma'] . ' - ' . ($rel['publicaciones']['enlace'] ?: 'Sin enlace'),
                'id_ticket' => $rel['id_ticket'],
                'id_publicacion' => $rel['id_publicacion'],
                'fecha_relacion' => date('Y-m-d', strtotime($rel['created_at'] ?? 'now'))
            ];
        }, $result['data']);
        
        echo json_encode($formattedRelations);
    } else {
        throw new Exception('Error al obtener las relaciones');
    }
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>