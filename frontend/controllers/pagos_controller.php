<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Obtiene la lista de pagos desde la API y aplica filtros localmente
 * 
 * @param int $skip Registros a omitir para paginación
 * @param int $limit Límite de registros a obtener
 * @param string $estado Estado de los pagos (opcional)
 * @return array Lista de pagos
 */
function getPagos($skip = 0, $limit = 100, $estado = null) {
    $api = new ApiClient();
    
    // Obtener todos los pagos sin filtrar
    $pagos = $api->get('/pagos/', ['skip' => 0, 'limit' => 500]);
    
    // Si no hay pagos, devolver un array vacío
    if (!$pagos) {
        return [];
    }
    
    // Si es un objeto único, convertirlo a array
    if (is_object($pagos) && isset($pagos->id)) {
        $pagos = [$pagos];
    }
    
    // Aplicar filtros en PHP
    if ($estado) {
        $pagosFiltrados = [];
        
        foreach ($pagos as $pago) {
            // Filtro por estado
            if (isset($pago['estado']) && strtoupper($pago['estado']) === strtoupper($estado)) {
                $pagosFiltrados[] = $pago;
            }
        }
        
        $pagos = $pagosFiltrados;
    }
    
    // Ordenar pagos por ID descendente (más recientes primero)
    usort($pagos, function($a, $b) {
        return $b['id'] - $a['id'];
    });
    
    // Aplicar paginación
    return array_slice($pagos, $skip, $limit);
}

/**
 * Obtiene los detalles de un pago específico
 * 
 * @param int $id ID del pago
 * @return array|null Detalles del pago o null si no existe
 */
function getPago($id) {
    $api = new ApiClient();
    return $api->get('/pagos/' . $id);
}

/**
 * Obtiene los pagos de un estudiante específico
 * 
 * @param int $cedula Cédula del estudiante
 * @return array Lista de pagos del estudiante
 */
function getPagosEstudiante($cedula) {
    $api = new ApiClient();
    $pagos = $api->get('/pagos/estudiante/' . $cedula);
    return $pagos ?: [];
}

/**
 * Crea un nuevo registro de pago
 * 
 * @param array $datos Datos del pago
 * @return array|false Respuesta de la API o false si hay error
 */
function crearPago($datos) {
    $api = new ApiClient();
    return $api->post('/pagos/', $datos);
}

/**
 * Registra un nuevo pago para un estudiante y cuota específicos
 * 
 * @param int $estudiante_cedula Cédula del estudiante
 * @param int $cuota_id ID de la cuota
 * @return array|false Respuesta de la API o false si hay error
 */
function registrarPago($estudiante_cedula, $cuota_id) {
    $api = new ApiClient();
    return $api->post("/pagos/registrar/{$estudiante_cedula}/{$cuota_id}", []);
}

/**
 * Marca un pago como pagado
 * 
 * @param int $id ID del pago
 * @return array|false Respuesta de la API o false si hay error
 */
function marcarPagado($id) {
    $api = new ApiClient();
    return $api->patch("/pagos/{$id}/marcar_pagado", []);
}

/**
 * Marca un pago como vencido
 * 
 * @param int $id ID del pago
 * @return array|false Respuesta de la API o false si hay error
 */
function marcarVencido($id) {
    $api = new ApiClient();
    return $api->patch("/pagos/{$id}/marcar_vencido", []);
}

/**
 * Actualiza los datos de un pago
 * 
 * @param int $id ID del pago
 * @param array $datos Datos a actualizar
 * @return array|false Respuesta de la API o false si hay error
 */
function actualizarPago($id, $datos) {
    $api = new ApiClient();
    return $api->put('/pagos/' . $id, $datos);
}

/**
 * Obtiene la lista de cuotas disponibles
 * 
 * @return array Lista de cuotas
 */
function getCuotas() {
    $api = new ApiClient();
    $cuotas = $api->get('/cuotas/');
    return $cuotas ?: [];
}
