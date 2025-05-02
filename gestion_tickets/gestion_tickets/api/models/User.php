<?php
class User {
    private $conn;
    private $table = 'usuarios';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        // Verificar que los datos requeridos están presentes
        if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['contrasena']) || !isset($data['rol'])) {
            throw new Exception("Faltan datos requeridos");
        }
        
        // Preparar los datos para Supabase
        $userData = [
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'contrasena' => password_hash($data['contrasena'], PASSWORD_DEFAULT),
            'rol' => $data['rol']
        ];
        
        try {
            // Llamamos directamente al request para INSERT
            $endpoint = "/rest/v1/{$this->table}";
            $result = $this->conn->request('POST', $endpoint, $userData);
            
            if (!empty($result)) {
                return [
                    'success' => true,
                    'message' => 'Usuario creado exitosamente',
                    'id' => $result[0]['id_usuario'] ?? $this->conn->lastInsertId()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error al crear el usuario'
            ];
        } catch(Exception $e) {
            throw new Exception("Error en la base de datos: " . $e->getMessage());
        }
    }

    public function read() {
        try {
            // Consulta directa a Supabase
            $endpoint = "/rest/v1/{$this->table}";
            $params = ['order' => 'fecha_creacion.desc'];
            $result = $this->conn->request('GET', $endpoint, null, $params);
            
            return $result;
        } catch(Exception $e) {
            throw new Exception("Error al leer usuarios: " . $e->getMessage());
        }
    }

    public function readOne($id) {
        try {
            // Consulta directa a Supabase para un usuario específico
            $endpoint = "/rest/v1/{$this->table}";
            $params = ['id_usuario' => "eq.$id"];
            $result = $this->conn->request('GET', $endpoint, null, $params);
            
            if (!empty($result)) {
                return $result[0];
            }
            
            return null;
        } catch(Exception $e) {
            throw new Exception("Error al leer usuario: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        $updateData = [];

        if(!empty($data['nombre'])) {
            $updateData['nombre'] = $data['nombre'];
        }
        if(!empty($data['email'])) {
            $updateData['email'] = $data['email'];
        }
        if(!empty($data['contrasena'])) {
            $updateData['contrasena'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);
        }
        if(!empty($data['rol'])) {
            $updateData['rol'] = $data['rol'];
        }

        if(empty($updateData)) {
            return [
                'success' => false, 
                'message' => 'No hay campos para actualizar'
            ];
        }

        try {
            // Actualizar en Supabase
            $endpoint = "/rest/v1/{$this->table}";
            $params = ['id_usuario' => "eq.$id"];
            $result = $this->conn->request('PATCH', $endpoint, $updateData, $params);
            
            if ($result !== null) {
                return [
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error al actualizar usuario'
            ];
        } catch(Exception $e) {
            throw new Exception("Error al actualizar: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            // Eliminar en Supabase
            $endpoint = "/rest/v1/{$this->table}";
            $params = ['id_usuario' => "eq.$id"];
            $result = $this->conn->request('DELETE', $endpoint, null, $params);
            
            if ($result !== null) {
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado exitosamente'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error al eliminar usuario'
            ];
        } catch(Exception $e) {
            throw new Exception("Error al eliminar: " . $e->getMessage());
        }
    }
}
?>