<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/User.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                $result = $user->readOne($_GET['id']);
                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }
            } else {
                $result = $user->read();
                echo json_encode(['success' => true, 'data' => $result]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) {
                throw new Exception("Datos inválidos");
            }
            
            if (empty($data['nombre']) || empty($data['email']) || empty($data['contrasena']) || empty($data['rol'])) {
                throw new Exception("Faltan campos requeridos");
            }
            
            $result = $user->create($data);
            echo json_encode($result);
            break;

        case 'PUT':
            if (!isset($_GET['id'])) {
                throw new Exception("ID no proporcionado");
            }
            
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) {
                throw new Exception("Datos inválidos");
            }
            
            $result = $user->update($_GET['id'], $data);
            echo json_encode($result);
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception("ID no proporcionado");
            }
            
            $result = $user->delete($_GET['id']);
            echo json_encode($result);
            break;

        default:
            throw new Exception("Método no permitido");
    }

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>