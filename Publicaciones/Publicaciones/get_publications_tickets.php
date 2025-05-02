<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT pt.*, p.plataforma, p.enlace 
        FROM publicaciones_tickets pt 
        JOIN publicaciones p ON pt.id_publicacion = p.id_publicacion 
        ORDER BY pt.id_publicacion DESC
    ");
    
    $relations = $stmt->fetchAll();
    
    // Formatear los datos para la tabla
    $formattedRelations = array_map(function($rel) {
        return [
            'id_relacion' => $rel['id_publicacion'] . '-' . $rel['id_ticket'],
            'publicacion' => $rel['plataforma'] . ' - ' . ($rel['enlace'] ?: 'Sin enlace'),
            'id_ticket' => $rel['id_ticket'],
            'id_publicacion' => $rel['id_publicacion'],
            'fecha_relacion' => date('Y-m-d', strtotime($rel['fecha_relacion'] ?? 'now'))
        ];
    }, $relations);
    
    echo json_encode($formattedRelations);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
