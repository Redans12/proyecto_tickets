<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Obtener los IDs de la URL
    $id_publicacion = isset($_GET['id_publicacion']) ? $_GET['id_publicacion'] : null;
    $id_ticket = isset($_GET['id_ticket']) ? $_GET['id_ticket'] : null;
    
    if (!$id_publicacion || !$id_ticket) {
        throw new Exception('Se requieren ambos IDs');
    }

    // Consultar la relación específica
    $stmt = $pdo->prepare("
        SELECT pt.*, p.plataforma, p.enlace 
        FROM publicaciones_tickets pt 
        JOIN publicaciones p ON pt.id_publicacion = p.id_publicacion 
        WHERE pt.id_publicacion = ? AND pt.id_ticket = ?
    ");
    
    $stmt->execute([$id_publicacion, $id_ticket]);
    $relation = $stmt->fetch();
    
    if ($relation) {
        echo json_encode([
            'success' => true,
            'data' => [
                'id_publicacion' => $relation['id_publicacion'],
                'id_ticket' => $relation['id_ticket'],
                'plataforma' => $relation['plataforma'],
                'enlace' => $relation['enlace']
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
