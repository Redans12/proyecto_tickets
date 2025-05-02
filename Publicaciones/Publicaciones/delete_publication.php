<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (!$id) {
        throw new Exception('ID no proporcionado');
    }

    $stmt = $pdo->prepare("DELETE FROM publicaciones WHERE id_publicacion = ?");
    $success = $stmt->execute([$id]);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar la publicación'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>