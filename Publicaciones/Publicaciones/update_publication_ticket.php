<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Validación de datos
    if (empty($_POST['id_publicacion'])) {
        throw new Exception('El ID de la publicación es requerido');
    }

    if (empty($_POST['plataforma'])) {
        throw new Exception('La plataforma es requerida');
    }

    $id = $_POST['id_publicacion'];
    $plataforma = $_POST['plataforma'];
    $enlace = $_POST['enlace'] ?: null;
    
    // Verificar la conexión
    if (!$pdo) {
        throw new Exception('Error de conexión a la base de datos');
    }

    $stmt = $pdo->prepare("UPDATE publicaciones SET plataforma = ?, enlace = ? WHERE id_publicacion = ?");
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta');
    }

    $success = $stmt->execute([$plataforma, $enlace, $id]);
    
    if (!$success) {
        $error = $stmt->errorInfo();
        throw new Exception('Error en la base de datos: ' . $error[2]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Publicación actualizada exitosamente'
    ]);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
