<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Obtiene la lista de controladores desde la API
 * 
 * @param int $skip Registros a omitir para paginación
 * @param int $limit Límite de registros a obtener
 * @param string $tipo Tipo de controlador (READER, WRITER, etc.)
 * @return array Lista de controladores
 */
function getControladores($skip = 0, $limit = 100, $tipo = null) {
    $api = new ApiClient();
    $params = [
        'skip' => $skip,
        'limit' => $limit
    ];
    
    if ($tipo) {
        $params['tipo'] = $tipo;
    }
    
    $controladores = $api->get('/controladores/', $params);
    return $controladores ?: [];
}

/**
 * Obtiene los detalles de un controlador específico
 * 
 * @param int $id ID del controlador
 * @return array|null Detalles del controlador o null si no existe
 */
function getControlador($id) {
    $api = new ApiClient();
    return $api->get('/controladores/' . $id);
}

/**
 * Crea un nuevo controlador
 * 
 * @param array $datos Datos del controlador
 * @return array|false Respuesta de la API o false si hay error
 */
function crearControlador($datos) {
    $api = new ApiClient();
    return $api->post('/controladores/', $datos);
}

/**
 * Actualiza los datos de un controlador
 * 
 * @param int $id ID del controlador
 * @param array $datos Datos a actualizar
 * @return array|false Respuesta de la API o false si hay error
 */
function actualizarControlador($id, $datos) {
    $api = new ApiClient();
    return $api->put('/controladores/' . $id, $datos);
}

/**
 * Elimina un controlador
 * 
 * @param int $id ID del controlador
 * @return bool true si se eliminó correctamente, false en caso contrario
 */
function eliminarControlador($id) {
    $api = new ApiClient();
    $response = $api->delete('/controladores/' . $id);
    
    // La API devuelve 204 No Content en caso de éxito (lo que se traduce en null)
    return $response === null || $response === true || (isset($response['success']) && $response['success']);
}

/**
 * Activa un controlador
 * 
 * @param int $id ID del controlador
 * @return array|false Respuesta de la API o false si hay error
 */
function activarControlador($id) {
    $api = new ApiClient();
    return $api->patch('/controladores/' . $id . '/activar', []);
}

/**
 * Desactiva un controlador
 * 
 * @param int $id ID del controlador
 * @return array|false Respuesta de la API o false si hay error
 */
function desactivarControlador($id) {
    $api = new ApiClient();
    return $api->patch('/controladores/' . $id . '/desactivar', []);
}
