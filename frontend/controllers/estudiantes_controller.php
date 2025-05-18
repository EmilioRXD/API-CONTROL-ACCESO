<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Obtiene la lista de estudiantes desde la API
 * 
 * @param int $skip Registros a omitir para paginación
 * @param int $limit Límite de registros a obtener
 * @return array Lista de estudiantes
 */
function getEstudiantes($skip = 0, $limit = 100) {
    $api = new ApiClient();
    $params = [
        'skip' => $skip,
        'limit' => $limit
    ];
    
    $estudiantes = $api->get('/estudiantes/', $params);
    return $estudiantes ?: [];
}

/**
 * Obtiene los detalles de un estudiante específico
 * 
 * @param int $cedula Cédula del estudiante
 * @return array|null Detalles del estudiante o null si no existe
 */
function getEstudiante($cedula) {
    $api = new ApiClient();
    return $api->get('/estudiantes/' . $cedula);
}

/**
 * Crea un nuevo estudiante
 * 
 * @param array $datos Datos del estudiante
 * @return array|false Respuesta de la API o false si hay error
 */
function crearEstudiante($datos) {
    $api = new ApiClient();
    return $api->post('/estudiantes/', $datos);
}

/**
 * Actualiza los datos de un estudiante
 * 
 * @param int $cedula Cédula del estudiante
 * @param array $datos Datos a actualizar
 * @return array|false Respuesta de la API o false si hay error
 */
function actualizarEstudiante($cedula, $datos) {
    $api = new ApiClient();
    return $api->put('/estudiantes/' . $cedula, $datos);
}

/**
 * Elimina un estudiante
 * 
 * @param int $cedula Cédula del estudiante
 * @return bool true si se eliminó correctamente, false en caso contrario
 */
function eliminarEstudiante($cedula) {
    $api = new ApiClient();
    $response = $api->delete('/estudiantes/' . $cedula);
    
    // La API devuelve 204 No Content en caso de éxito (lo que se traduce en null)
    return $response === null || $response === true || (isset($response['success']) && $response['success']);
}

/**
 * Obtiene la lista de carreras para el formulario de estudiantes
 * 
 * @return array Lista de carreras
 */
function getCarreras() {
    $api = new ApiClient();
    $carreras = $api->get('/carreras/');
    return $carreras ?: [];
}
