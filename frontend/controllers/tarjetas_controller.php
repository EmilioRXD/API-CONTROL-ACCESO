<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Obtiene la lista de tarjetas desde la API
 * 
 * @param int $skip Registros a omitir para paginación
 * @param int $limit Límite de registros a obtener
 * @return array Lista de tarjetas
 */
function getTarjetas($skip = 0, $limit = 100) {
    $api = new ApiClient();
    $params = [
        'skip' => $skip,
        'limit' => $limit
    ];
    
    $tarjetas = $api->get('/tarjetas/', $params);
    return $tarjetas ?: [];
}

/**
 * Obtiene los detalles de una tarjeta específica
 * 
 * @param int $id ID de la tarjeta
 * @return array|null Detalles de la tarjeta o null si no existe
 */
function getTarjeta($id) {
    $api = new ApiClient();
    return $api->get('/tarjetas/' . $id);
}

/**
 * Obtiene las tarjetas de un estudiante específico
 * 
 * @param int $cedula Cédula del estudiante
 * @return array Lista de tarjetas del estudiante
 */
function getTarjetasEstudiante($cedula) {
    $api = new ApiClient();
    $tarjetas = $api->get('/tarjetas/estudiante/' . $cedula);
    return $tarjetas ?: [];
}

/**
 * Asigna una nueva tarjeta a un estudiante
 * 
 * @param array $datos Datos de la tarjeta
 * @return array|false Respuesta de la API o false si hay error
 */
function asignarTarjeta($datos) {
    $api = new ApiClient();
    return $api->post('/tarjetas/', $datos);
}

/**
 * Actualiza los datos de una tarjeta
 * 
 * @param int $id ID de la tarjeta
 * @param array $datos Datos a actualizar
 * @return array|false Respuesta de la API o false si hay error
 */
function actualizarTarjeta($id, $datos) {
    $api = new ApiClient();
    return $api->put('/tarjetas/' . $id, $datos);
}

/**
 * Elimina una tarjeta
 * 
 * @param int $id ID de la tarjeta
 * @return bool true si se eliminó correctamente, false en caso contrario
 */
function eliminarTarjeta($id) {
    $api = new ApiClient();
    $response = $api->delete('/tarjetas/' . $id);
    
    // La API devuelve 204 No Content en caso de éxito (lo que se traduce en null)
    return $response === null || $response === true || (isset($response['success']) && $response['success']);
}

/**
 * Activa una tarjeta
 * 
 * @param int $id ID de la tarjeta
 * @return array|false Respuesta de la API o false si hay error
 */
function activarTarjeta($id) {
    $api = new ApiClient();
    return $api->patch('/tarjetas/' . $id . '/activar', []);
}

/**
 * Desactiva una tarjeta
 * 
 * @param int $id ID de la tarjeta
 * @return array|false Respuesta de la API o false si hay error
 */
function desactivarTarjeta($id) {
    $api = new ApiClient();
    return $api->patch('/tarjetas/' . $id . '/desactivar', []);
}

/**
 * Obtiene la lista de escritores de tarjetas (controladores)
 * 
 * @return array Lista de controladores
 */
function getEscritoresTarjetas() {
    $api = new ApiClient();
    $controladores = $api->get('/controladores/', ['tipo' => 'WRITER']);
    return $controladores ?: [];
}
