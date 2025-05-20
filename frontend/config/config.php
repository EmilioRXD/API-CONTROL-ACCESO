<?php
session_start();

// Configuración de la API
define('API_BASE_URL', 'http://localhost:8090'); // Ajustar según la ubicación de tu API
define('API_TOKEN_ENDPOINT', '/token');
define('API_TIMEOUT', 30);

// Rutas principales de la aplicación
define('BASE_PATH', dirname(__DIR__));
define('URL_BASE', '/proyecto'); // Ajustar según tu configuración

// Configuración de seguridad
define('SESSION_JWT_KEY', 'jwt_token');

// Función para obtener el JWT de la sesión
function getJWTFromSession() {
    return isset($_SESSION[SESSION_JWT_KEY]) ? $_SESSION[SESSION_JWT_KEY] : null;
}

// Función para guardar la configuración del sistema
function saveConfig($config) {
    $configFile = BASE_PATH . '/config/app_config.json';
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
}

// Función para cargar la configuración del sistema
function loadConfig() {
    $configFile = BASE_PATH . '/config/app_config.json';
    if (file_exists($configFile)) {
        return json_decode(file_get_contents($configFile), true);
    }
    return [
        'api_url' => API_BASE_URL,
        'timeout' => API_TIMEOUT
    ];
}
