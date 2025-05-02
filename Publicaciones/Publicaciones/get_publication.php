<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $id = $_GET['id'];
    
    $stmt = $pdo->prepare("DELETE FROM publicaciones WHERE id_publicacion = ?");
    $success = $stmt->execute([$id]);
    
    echo json_encode(['success' => $success]);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>