<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Validar que los datos necesarios estén presentes
    if (!isset($_POST['plataforma'])) {
        throw new Exception('La plataforma es requerida');
    }

    // Obtener el primer usuario como usuario por defecto (puedes modificar esto según tu lógica de autenticación)
    $stmtUser = $pdo->query("SELECT id_usuario FROM usuarios WHERE rol = 'Jefe de Operaciones' LIMIT 1");
    $usuario = $stmtUser->fetch();
    
    if (!$usuario) {
        throw new Exception('No hay usuarios con rol adecuado en el sistema');
    }

    $id_usuario = $usuario['id_usuario'];
    $plataforma = $_POST['plataforma'];
    $enlace = isset($_POST['enlace']) ? $_POST['enlace'] : null;

    // Preparar la consulta SQL con los campos correctos
    $sql = "INSERT INTO publicaciones (id_usuario, plataforma, enlace, fecha_publicacion) 
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta con los valores
    $success = $stmt->execute([$id_usuario, $plataforma, $enlace]);

    if ($success) {
        $newId = $pdo->lastInsertId();
        echo json_encode([
            'success' => true,
            'id' => $newId,
            'message' => 'Publicación creada exitosamente'
        ]);
    } else {
        throw new Exception('Error al crear la publicación');
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