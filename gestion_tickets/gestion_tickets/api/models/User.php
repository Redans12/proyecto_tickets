<?php
class User {
    private $conn;
    private $table = 'usuarios';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, email, contrasena, rol) 
                  VALUES (:nombre, :email, :contrasena, :rol)";

        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':email', $data['email']);
            $hashedPassword = password_hash($data['contrasena'], PASSWORD_DEFAULT);
            $stmt->bindParam(':contrasena', $hashedPassword);
            $stmt->bindParam(':rol', $data['rol']);

            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Usuario creado exitosamente',
                    'id' => $this->conn->lastInsertId()
                ];
            }
            return [
                'success' => false,
                'message' => 'Error al crear el usuario'
            ];
        } catch(PDOException $e) {
            throw new Exception("Error en la base de datos: " . $e->getMessage());
        }
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY fecha_creacion DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error al leer usuarios: " . $e->getMessage());
        }
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_usuario = :id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Error al leer usuario: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        $updateFields = [];
        $params = [];

        if(!empty($data['nombre'])) {
            $updateFields[] = "nombre = :nombre";
            $params[':nombre'] = $data['nombre'];
        }
        if(!empty($data['email'])) {
            $updateFields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        if(!empty($data['contrasena'])) {
            $updateFields[] = "contrasena = :contrasena";
            $params[':contrasena'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);
        }
        if(!empty($data['rol'])) {
            $updateFields[] = "rol = :rol";
            $params[':rol'] = $data['rol'];
        }

        if(empty($updateFields)) {
            return [
                'success' => false, 
                'message' => 'No hay campos para actualizar'
            ];
        }

        $query = "UPDATE " . $this->table . " SET " . implode(", ", $updateFields) . " WHERE id_usuario = :id";
        $params[':id'] = $id;

        try {
            $stmt = $this->conn->prepare($query);
            if($stmt->execute($params)) {
                return [
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente'
                ];
            }
            return [
                'success' => false,
                'message' => 'Error al actualizar usuario'
            ];
        } catch(PDOException $e) {
            throw new Exception("Error al actualizar: " . $e->getMessage());
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_usuario = :id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado exitosamente'
                ];
            }
            return [
                'success' => false,
                'message' => 'Error al eliminar usuario'
            ];
        } catch(PDOException $e) {
            throw new Exception("Error al eliminar: " . $e->getMessage());
        }
    }
}
?>