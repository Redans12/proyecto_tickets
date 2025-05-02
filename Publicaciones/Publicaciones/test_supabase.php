<?php
require_once 'config_supabase.php';

// Test data
$testData = [
    'plataforma' => 'Test Platform',
    'enlace' => 'https://example.com',
    'fecha_publicacion' => date('Y-m-d H:i:s'),
    'id_usuario' => 1
];

// Create Supabase instance
$supabase = new SupabaseConfig();

// Try direct API call
try {
    $result = $supabase->createPublication($testData);
    echo "Result: " . json_encode($result, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>