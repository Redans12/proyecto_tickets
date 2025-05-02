<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id_publicacion, plataforma, enlace, fecha_publicacion 
                         FROM publicaciones 
                         ORDER BY fecha_publicacion DESC");
    $publications = $stmt->fetchAll();
    echo json_encode($publications);
} catch(PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error al obtener las publicaciones: ' . $e->getMessage()
    ]);
}
?>