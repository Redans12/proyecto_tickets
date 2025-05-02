<?php
class Database {
    private $supabase_url = 'https://onbgqjndemplsgxdaltr.supabase.co';
    private $supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im9uYmdxam5kZW1wbHNneGRhbHRyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDM1MTcxMTMsImV4cCI6MjA1OTA5MzExM30.HnBHKLOu7yY5H9xHyqeCV0S45fghKfgyGrL12oDRXWw';
    private $conn = null;

    public function getConnection() {
        if ($this->conn === null) {
            $this->conn = new SupabaseClient($this->supabase_url, $this->supabase_key);
        }
        return $this->conn;
    }
}

// Cliente para interactuar con Supabase
class SupabaseClient {
    private $supabase_url;
    private $supabase_key;
    private $last_insert_id = null;
    
    public function __construct($url, $key) {
        $this->supabase_url = $url;
        $this->supabase_key = $key;
    }
    
    // Simulación de métodos de PDO para mantener compatibilidad
    public function setAttribute($attribute, $value) {
        return true;
    }
    
    public function prepare($query) {
        return new SupabaseStatement($query, $this);
    }
    
    public function lastInsertId() {
        return $this->last_insert_id;
    }
    
    public function setLastInsertId($id) {
        $this->last_insert_id = $id;
    }
    
    // Método para hacer llamadas a la API de Supabase
    public function request($method, $endpoint, $data = null, $params = []) {
        $url = $this->supabase_url . $endpoint;
        
        // Añadir parámetros a la URL si existen
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $url .= (strpos($url, '?') !== false) ? '&' . $queryString : '?' . $queryString;
        }
        
        $headers = [
            'apikey: ' . $this->supabase_key,
            'Authorization: Bearer ' . $this->supabase_key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($http_code >= 400) {
            throw new PDOException("Error en la solicitud a Supabase: $http_code - $response");
        }
        
        $result = json_decode($response, true);
        
        // Si es una inserción, guardar el ID
        if ($method === 'POST' && !empty($result) && isset($result[0]['id'])) {
            $this->setLastInsertId($result[0]['id']);
        }
        
        return $result;
    }
}

// Clase para manejar las consultas preparadas
class SupabaseStatement {
    private $query;
    private $supabase_client;
    private $params = [];
    private $result_data = [];
    private $current_row = 0;
    private $fetch_mode = PDO::FETCH_ASSOC;
    
    public function __construct($query, $supabase_client) {
        $this->query = $query;
        $this->supabase_client = $supabase_client;
    }
    
    public function bindParam($param, &$var, $type = null) {
        $this->params[$param] = $var;
        return true;
    }
    
    public function bindValue($param, $value, $type = null) {
        $this->params[$param] = $value;
        return true;
    }
    
    public function execute($params = null) {
        if ($params) {
            $this->params = array_merge($this->params, $params);
        }
        
        // Analizar la consulta SQL y convertirla en llamadas a la API de Supabase
        if ($this->isSelectQuery()) {
            $this->executeSelect();
        } elseif ($this->isInsertQuery()) {
            $this->executeInsert();
        } elseif ($this->isUpdateQuery()) {
            $this->executeUpdate();
        } elseif ($this->isDeleteQuery()) {
            $this->executeDelete();
        }
        
        return true;
    }
    
    public function fetch($fetch_style = null) {
        if ($fetch_style === null) {
            $fetch_style = $this->fetch_mode;
        }
        
        if ($this->current_row >= count($this->result_data)) {
            return false;
        }
        
        $row = $this->result_data[$this->current_row++];
        
        return $row;
    }
    
    public function fetchAll($fetch_style = null) {
        if ($fetch_style === null) {
            $fetch_style = $this->fetch_mode;
        }
        
        return $this->result_data;
    }
    
    public function fetchColumn() {
        if ($this->current_row >= count($this->result_data)) {
            return false;
        }
        
        $row = $this->result_data[$this->current_row++];
        
        return reset($row);
    }
    
    public function rowCount() {
        return count($this->result_data);
    }
    
    private function isSelectQuery() {
        return stripos($this->query, 'SELECT') === 0;
    }
    
    private function isInsertQuery() {
        return stripos($this->query, 'INSERT') === 0;
    }
    
    private function isUpdateQuery() {
        return stripos($this->query, 'UPDATE') === 0;
    }
    
    private function isDeleteQuery() {
        return stripos($this->query, 'DELETE') === 0;
    }
    
    private function executeSelect() {
        // Extraer tabla y condiciones
        preg_match('/FROM\s+([^\s,]+)/i', $this->query, $tableMatches);
        $table = $tableMatches[1];
        
        // Extraer condiciones WHERE
        $endpoint = "/rest/v1/$table";
        $queryParams = [];
        
        // Procesamos condiciones WHERE
        if (preg_match('/WHERE\s+(.+?)(?:ORDER\s+BY|LIMIT|GROUP\s+BY|$)/is', $this->query, $whereMatches)) {
            $whereClause = $whereMatches[1];
            
            // Manejar condición de igualdad simple (ejemplo: id = ?)
            if (preg_match('/([^\s=]+)\s*=\s*\?/i', $whereClause, $condMatches)) {
                $field = $condMatches[1];
                $paramIndex = 0; // Asumimos que es el primer parámetro
                
                if (isset($this->params[$paramIndex])) {
                    $queryParams[$field] = 'eq.' . $this->params[$paramIndex];
                }
            }
        }
        
        // Ejecutar la consulta
        try {
            $this->result_data = $this->supabase_client->request('GET', $endpoint, null, $queryParams);
        } catch (PDOException $e) {
            throw new PDOException("Error en la consulta SELECT: " . $e->getMessage());
        }
    }
    
    private function executeInsert() {
        // Extraer tabla
        preg_match('/INSERT\s+INTO\s+([^\s(]+)/i', $this->query, $tableMatches);
        $table = $tableMatches[1];
        
        // Extraer columnas y valores
        preg_match('/\(([^)]+)\)\s+VALUES\s+\(([^)]+)\)/i', $this->query, $matches);
        
        if (isset($matches[1]) && isset($matches[2])) {
            $columns = array_map('trim', explode(',', $matches[1]));
            $values = array_map('trim', explode(',', $matches[2]));
            
            $data = [];
            foreach ($columns as $i => $column) {
                $value = $values[$i];
                if ($value === '?') {
                    $data[$column] = $this->params[$i];
                } else {
                    // Manejar valores literales
                    $data[$column] = trim($value, "'\"");
                }
            }
            
            $endpoint = "/rest/v1/$table";
            
            try {
                $result = $this->supabase_client->request('POST', $endpoint, $data);
                $this->result_data = $result;
            } catch (PDOException $e) {
                throw new PDOException("Error en la consulta INSERT: " . $e->getMessage());
            }
        }
    }
    
    private function executeUpdate() {
        // Extraer tabla
        preg_match('/UPDATE\s+([^\s]+)/i', $this->query, $tableMatches);
        $table = $tableMatches[1];
        
        // Extraer campos para actualizar
        preg_match('/SET\s+(.+?)\s+WHERE/i', $this->query, $setMatches);
        $setClause = $setMatches[1];
        
        // Extraer condición WHERE
        preg_match('/WHERE\s+(.+?)$/i', $this->query, $whereMatches);
        $whereClause = $whereMatches[1];
        
        // Procesar cláusula SET para obtener los datos a actualizar
        $data = [];
        $setParts = explode(',', $setClause);
        
        foreach ($setParts as $i => $part) {
            if (preg_match('/([^\s=]+)\s*=\s*\?/i', $part, $matches)) {
                $field = $matches[1];
                $data[$field] = $this->params[$i];
            }
        }
        
        // Procesar condición WHERE para generar filtro
        $endpoint = "/rest/v1/$table";
        $queryParams = [];
        
        if (preg_match('/([^\s=]+)\s*=\s*\?/i', $whereClause, $matches)) {
            $field = $matches[1];
            $paramIndex = count($setParts); // Asumimos que es el siguiente parámetro después de los SET
            
            if (isset($this->params[$paramIndex])) {
                $queryParams[$field] = 'eq.' . $this->params[$paramIndex];
            }
        }
        
        try {
            $this->result_data = $this->supabase_client->request('PATCH', $endpoint, $data, $queryParams);
        } catch (PDOException $e) {
            throw new PDOException("Error en la consulta UPDATE: " . $e->getMessage());
        }
    }
    
    private function executeDelete() {
        // Extraer tabla
        preg_match('/DELETE\s+FROM\s+([^\s]+)/i', $this->query, $tableMatches);
        $table = $tableMatches[1];
        
        // Extraer condición WHERE
        $endpoint = "/rest/v1/$table";
        $queryParams = [];
        
        if (preg_match('/WHERE\s+(.+?)$/i', $this->query, $whereMatches)) {
            $whereClause = $whereMatches[1];
            
            if (preg_match('/([^\s=]+)\s*=\s*\?/i', $whereClause, $matches)) {
                $field = $matches[1];
                $paramIndex = 0; // Asumimos que es el primer parámetro
                
                if (isset($this->params[$paramIndex])) {
                    $queryParams[$field] = 'eq.' . $this->params[$paramIndex];
                }
            }
        }
        
        try {
            $this->result_data = $this->supabase_client->request('DELETE', $endpoint, null, $queryParams);
        } catch (PDOException $e) {
            throw new PDOException("Error en la consulta DELETE: " . $e->getMessage());
        }
    }
    
    public function setFetchMode($mode) {
        $this->fetch_mode = $mode;
        return true;
    }
}

// Añadimos estas constantes para mantener compatibilidad
if (!defined('PDO::ATTR_ERRMODE')) {
    define('PDO::ATTR_ERRMODE', 3);
    define('PDO::ERRMODE_EXCEPTION', 2);
    define('PDO::FETCH_ASSOC', 2);
    define('PDO::ATTR_DEFAULT_FETCH_MODE', 19);
}

class PDOException extends Exception {}
?>