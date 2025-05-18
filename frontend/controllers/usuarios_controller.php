<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Obtiene la lista de usuarios desde la API
 * 
 * @param int $skip Registros a omitir para paginación
 * @param int $limit Límite de registros a obtener
 * @return array Lista de usuarios
 */
function getUsuarios($skip = 0, $limit = 100) {
    $api = new ApiClient();
    $params = [
        'skip' => $skip,
        'limit' => $limit
    ];
    
    $usuarios = $api->get('/usuarios/', $params);
    return $usuarios ?: [];
}

/**
 * Obtiene los detalles de un usuario específico
 * 
 * @param int $id ID del usuario
 * @return array|null Detalles del usuario o null si no existe
 */
function getUsuario($id) {
    $api = new ApiClient();
    return $api->get('/usuarios/' . $id);
}

/**
 * Crea un nuevo usuario
 * 
 * @param array $datos Datos del usuario
 * @return array|false Respuesta de la API o false si hay error
 */
function crearUsuario($datos) {
    $api = new ApiClient();
    return $api->post('/usuarios/', $datos);
}

/**
 * Actualiza los datos de un usuario
 * 
 * @param int $id ID del usuario
 * @param array $datos Datos a actualizar
 * @return array|false Respuesta de la API o false si hay error
 */
function actualizarUsuario($id, $datos) {
    $api = new ApiClient();
    return $api->put('/usuarios/' . $id, $datos);
}

/**
 * Elimina un usuario
 * 
 * @param int $id ID del usuario
 * @return bool true si se eliminó correctamente, false en caso contrario
 */
function eliminarUsuario($id) {
    $api = new ApiClient();
    $response = $api->delete('/usuarios/' . $id);
    
    // La API devuelve 204 No Content en caso de éxito (lo que se traduce en null)
    return $response === null || $response === true || (isset($response['success']) && $response['success']);
}

/**
 * Activa o desactiva un usuario
 * 
 * @param int $id ID del usuario
 * @param bool $activar true para activar, false para desactivar
 * @return array|false Respuesta de la API o false si hay error
 */
function cambiarEstadoUsuario($id, $activar = true) {
    $api = new ApiClient();
    $endpoint = '/usuarios/' . $id . '/' . ($activar ? 'activar' : 'desactivar');
    return $api->patch($endpoint, []);
}
