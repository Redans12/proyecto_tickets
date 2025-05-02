<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (!$id) {
        throw new Exception('ID no proporcionado');
    }

    // Primero verificamos si la relación existe
    $checkStmt = $pdo->prepare("SELECT * FROM publicaciones_tickets WHERE id_publicacion = ? OR id_ticket = ?");
    $checkStmt->execute([$id, $id]);
    
    if (!$checkStmt->fetch()) {
        throw new Exception('La relación no existe');
    }

    // Si existe, procedemos a eliminarla
    $stmt = $pdo->prepare("DELETE FROM publicaciones_tickets WHERE id_publicacion = ? OR id_ticket = ?");
    $success = $stmt->execute([$id, $id]);
    
    if ($success) {
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
