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

/**
 * Guarda la configuración de acceso y período de gracia
 * 
 * @param array $datos Datos de configuración de acceso
 * @return array|false Respuesta de la API o false si hay error
 */
function guardarConfiguracionAcceso($datos) {
    // Verificar y formatear los datos
    $api = new ApiClient();
    $exito = true;
    $resultados = [];
    
    // Procesar PERIODO_GRACIA_DIAS (ID=1)
    if (isset($datos['PERIODO_GRACIA_DIAS'])) {
        // Asegurar que sea un número entero y esté entre 0 y 30
        $diasGracia = max(0, min(30, intval($datos['PERIODO_GRACIA_DIAS'])));
        
        // Usar PATCH con el ID específico y solo el valor
        $data = ['valor' => (string)$diasGracia];
        $resultado = $api->patch('/configuracion/1/valor', $data);
        
        if (!$resultado) {
            $exito = false;
        }
        $resultados[] = $resultado;
    }
    
    return $exito ? $resultados : false;
}

/**
 * Guarda la configuración de validación de cuotas
 * 
 * @param array $datos Datos de configuración de validación
 * @return array|false Respuesta de la API o false si hay error
 */
function guardarConfiguracionValidacion($datos) {
    // Verificar y formatear los datos
    $api = new ApiClient();
    
    // Procesar BLOQUEO_ACCESO_VENCIDOS (validación de cuotas, ID=2)
    $validacion = 'false'; // Por defecto está desactivado
    
    if (isset($datos['BLOQUEO_ACCESO_VENCIDOS']) && $datos['BLOQUEO_ACCESO_VENCIDOS'] === 'true') {
        $validacion = 'true';
    }
    
    // Usar PATCH con el ID específico y solo el valor
    $data = ['valor' => $validacion];
    $resultado = $api->patch('/configuracion/2/valor', $data);
    
    return $resultado ?: false;
}
