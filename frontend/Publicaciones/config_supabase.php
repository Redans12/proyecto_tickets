<?php
// Configuración de Supabase para PHP
class SupabaseConfig {
    // URL y clave de Supabase
    private $supabase_url = 'https://onbgqjndemplsgxdaltr.supabase.co';
    private $supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im9uYmdxam5kZW1wbHNneGRhbHRyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDM1MTcxMTMsImV4cCI6MjA1OTA5MzExM30.HnBHKLOu7yY5H9xHyqeCV0S45fghKfgyGrL12oDRXWw';

    // Función para realizar solicitudes a la API REST de Supabase
    public function request($method, $endpoint, $data = null) {
        $url = $this->supabase_url . $endpoint;
        $headers = [
            'apikey: ' . $this->supabase_key,
            'Authorization: Bearer ' . $this->supabase_key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
    
        // Log request details
        error_log("Supabase Request: $method $url");
        error_log("Headers: " . json_encode($headers));
        if ($data) {
            error_log("Data: " . json_encode($data));
        }
    
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
    
        // Add option to see more details about the request
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Log verbose output
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("Verbose curl output: " . $verboseLog);
        
        if ($http_code >= 400) {
            error_log("Error en la solicitud a Supabase: $http_code - $response");
        }
    
        curl_close($ch);
    
        return [
            'status' => $http_code,
            'data' => json_decode($response, true)
        ];
    }

    // Funciones específicas CRUD para la tabla 'publicaciones'
    
    // Obtener todas las publicaciones
    public function getPublications() {
        return $this->request('GET', '/rest/v1/publicaciones?select=*&order=fecha_publicacion.desc');
    }
    
    // Obtener una publicación por ID
    public function getPublication($id) {
        return $this->request('GET', "/rest/v1/publicaciones?id_publicacion=eq.$id&select=*");
    }
    
    // Crear una nueva publicación
    public function createPublication($data) {
        try {
            $response = $this->request('POST', '/rest/v1/publicaciones', $data);
            
            // Logging para depuración
            error_log('Respuesta completa de Supabase para createPublication: ' . json_encode($response));
            
            return $response;
        } catch(Exception $e) {
            error_log('Error en createPublication: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Actualizar una publicación
    public function updatePublication($id, $data) {
        return $this->request('PATCH', "/rest/v1/publicaciones?id_publicacion=eq.$id", $data);
    }
    
    // Eliminar una publicación
    public function deletePublication($id) {
        return $this->request('DELETE', "/rest/v1/publicaciones?id_publicacion=eq.$id");
    }
    
    // Funciones para la tabla 'publicaciones_tickets'
    
    // Obtener todas las relaciones publicaciones-tickets
    public function getPublicationsTickets() {
        return $this->request('GET', '/rest/v1/publicaciones_tickets?select=*,publicaciones(*)');
    }
    
    // Obtener una relación específica
    public function getPublicationTicket($id_publicacion, $id_ticket) {
        return $this->request('GET', "/rest/v1/publicaciones_tickets?id_publicacion=eq.$id_publicacion&id_ticket=eq.$id_ticket&select=*,publicaciones(*)");
    }
    
    // Crear una nueva relación
    public function createPublicationTicket($data) {
        return $this->request('POST', '/rest/v1/publicaciones_tickets', $data);
    }
    
    // Eliminar una relación
    public function deletePublicationTicket($id_publicacion, $id_ticket) {
        return $this->request('DELETE', "/rest/v1/publicaciones_tickets?id_publicacion=eq.$id_publicacion&id_ticket=eq.$id_ticket");
    }
}

// Crear una instancia global
$supabase = new SupabaseConfig();
?>