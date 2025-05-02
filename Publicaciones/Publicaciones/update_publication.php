<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['id_publicacion']) || !isset($_POST['plataforma'])) {
        throw new Exception('Datos incompletos');
    }

    $id = $_POST['id_publicacion'];
    $plataforma = $_POST['plataforma'];
    $enlace = isset($_POST['enlace']) ? $_POST['enlace'] : null;

    $sql = "UPDATE publicaciones 
            SET plataforma = ?, 
                enlace = ?
            WHERE id_publicacion = ?";
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$plataforma, $enlace, $id]);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al actualizar la publicación');
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