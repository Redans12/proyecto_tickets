<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $id_publicacion = $_POST['id_publicacion'];
    $id_ticket = $_POST['id_ticket'];
    
    // Verificar si la relación ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM publicaciones_tickets WHERE id_publicacion = ? AND id_ticket = ?");
    $stmt->execute([$id_publicacion, $id_ticket]);
    
    if ($stmt->fetchColumn() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Esta relación ya existe'
        ]);
        exit;
    }
    
    // Crear la nueva relación
    $stmt = $pdo->prepare("INSERT INTO publicaciones_tickets (id_publicacion, id_ticket) VALUES (?, ?)");
    $success = $stmt->execute([$id_publicacion, $id_ticket]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Relación creada correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear la relación'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
