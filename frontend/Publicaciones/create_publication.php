<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    // Validar que los datos necesarios estén presentes
    if (!isset($_POST['plataforma'])) {
        throw new Exception('La plataforma es requerida');
    }

    // Get first available user - note the singular "usuario" table name
    $usersResult = $supabase->request('GET', '/rest/v1/usuario?select=id_usuario&limit=1');
    
    if ($usersResult['status'] >= 200 && $usersResult['status'] < 300 && !empty($usersResult['data'])) {
        $defaultUserId = $usersResult['data'][0]['id_usuario'];
    } else {
        throw new Exception('No se encontraron usuarios en la base de datos');
    }

    // Preparar datos para Supabase
    $publicationData = [
        'plataforma' => $_POST['plataforma'],
        'enlace' => isset($_POST['enlace']) ? $_POST['enlace'] : null,
        'fecha_publicacion' => date('Y-m-d H:i:s')
    ];
    
    // Asignar ID de usuario
    if (isset($_POST['id_usuario'])) {
        $publicationData['id_usuario'] = $_POST['id_usuario'];
    } else {
        // Usar el ID del primer usuario encontrado
        $publicationData['id_usuario'] = $defaultUserId;
    }

    $result = $supabase->createPublication($publicationData);
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        echo json_encode([
            'success' => true,
            'id' => $result['data'][0]['id_publicacion'] ?? null,
            'message' => 'Publicación creada exitosamente'
        ]);
    } else {
        throw new Exception('Error al crear la publicación: ' . json_encode($result));
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>