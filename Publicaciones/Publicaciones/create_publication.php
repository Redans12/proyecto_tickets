<?php
require_once 'config_supabase.php';

header('Content-Type: application/json');

try {
    // Validar que los datos necesarios estén presentes
    if (!isset($_POST['plataforma'])) {
        throw new Exception('La plataforma es requerida');
    }

    // Preparar datos para Supabase
    $publicationData = [
        'plataforma' => $_POST['plataforma'],
        'enlace' => isset($_POST['enlace']) ? $_POST['enlace'] : null,
        'fecha_publicacion' => date('Y-m-d H:i:s')
    ];
    
    // Si tienes un id_usuario para asignar, puedes agregarlo aquí
    if (isset($_POST['id_usuario'])) {
        $publicationData['id_usuario'] = $_POST['id_usuario'];
    } else {
        // Obtener el primer usuario como usuario por defecto (esto dependerá de cómo tengas configurada tu tabla usuarios en Supabase)
        // Para este caso, simplemente asignaremos un ID 1 como valor por defecto
        $publicationData['id_usuario'] = 1;
    }

    $result = $supabase->createPublication($publicationData);
    
    if ($result['status'] >= 200 && $result['status'] < 300) {
        echo json_encode([
            'success' => true,
            'id' => $result['data'][0]['id_publicacion'] ?? null,
            'message' => 'Publicación creada exitosamente'
        ]);
    } else {
        throw new Exception('Error al crear la publicación');
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
