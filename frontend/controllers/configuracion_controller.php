<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Obtiene la configuración del sistema
 * 
 * @return array Configuración del sistema
 */
function getConfiguracion() {
    // Obtener configuración local
    $configLocal = loadConfig();
    
    // Obtener configuración desde la API
    $api = new ApiClient();
    $configAPI = $api->get('/configuracion/');
    
    // Combinar configuraciones (priorizar la local)
    $configuracion = [
        'local' => $configLocal,
        'api' => $configAPI ?: []
    ];
    
    return $configuracion;
}

/**
 * Guarda la configuración local del sistema
 * 
 * @param array $datos Datos de configuración
 * @return bool true si se guardó correctamente, false en caso contrario
 */
function guardarConfiguracionLocal($datos) {
    try {
        // Asegurar que los datos tengan los campos requeridos
        $config = [
            'api_url' => $datos['api_url'] ?? API_BASE_URL,
            'timeout' => isset($datos['timeout']) ? (int)$datos['timeout'] : API_TIMEOUT
        ];
        
        // Guardar configuración
        saveConfig($config);
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Actualiza la configuración en la API
 * 
 * @param array $datos Datos de configuración
 * @return array|false Respuesta de la API o false si hay error
 */
function actualizarConfiguracionAPI($datos) {
    $api = new ApiClient();
    return $api->put('/configuracion/', $datos);
}
