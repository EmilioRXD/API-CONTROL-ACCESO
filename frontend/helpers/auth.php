<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Verifica si el usuario está autenticado
 * 
 * @return bool true si está autenticado, false en caso contrario
 */
function isAuthenticated() {
    // Verificar si existe el token
    if (!isset($_SESSION[SESSION_JWT_KEY]) || empty($_SESSION[SESSION_JWT_KEY])) {
        return false;
    }
    
        // No realizar validación adicional del token para evitar llamadas innecesarias a la API
    // La validación real se realizará cuando se hagan peticiones a endpoints protegidos
    return true;
}

/**
 * Redirige a la página de login si no está autenticado
 */
function requireAuth() {
    if (!isAuthenticated()) {
        // Guardar la URL actual para redirigir después del login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        // Indicar que la sesión ha expirado o el token es inválido
        $_SESSION['auth_message'] = 'Su sesión ha expirado o no tiene autorización. Por favor, inicie sesión nuevamente.';
        
        // Forzar redireccionamiento al login
        header('Location: ' . URL_BASE . '/public/login.php?session_expired=1');
        exit;
    }
}

/**
 * Realiza el proceso de login
 * 
 * @param string $email Correo electrónico
 * @param string $password Contraseña
 * @return bool true si el login es exitoso, false en caso contrario
 */
function doLogin($email, $password) {
    $api = new ApiClient();
    $response = $api->login($email, $password);
    
    if ($response && isset($response['access_token'])) {
        $_SESSION[SESSION_JWT_KEY] = $response['access_token'];
        return true;
    }
    
    return false;
}

/**
 * Cierra la sesión
 */
function logout() {
    unset($_SESSION[SESSION_JWT_KEY]);
    session_destroy();
}

/**
 * Obtiene el error de la API si existe
 */
function getApiError() {
    return isset($_SESSION['api_error']) ? $_SESSION['api_error'] : null;
}

/**
 * Limpia el error de la API
 */
function clearApiError() {
    unset($_SESSION['api_error']);
}
