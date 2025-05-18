<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Obtiene los datos necesarios para el dashboard
 * 
 * @return array Datos para el dashboard
 */
function getDashboardData() {
    $api = new ApiClient();
    $data = [
        'total_estudiantes' => 0,
        'total_tarjetas' => 0,
        'total_pagos' => 0,
        'total_controladores' => 0,
        'registros_recientes' => [],
        'pagos_pendientes' => []
    ];
    
    // Obtener totales
    $estudiantes = $api->get('/estudiantes/');
    if ($estudiantes) {
        $data['total_estudiantes'] = count($estudiantes);
    }
    
    $tarjetas = $api->get('/tarjetas/');
    if ($tarjetas) {
        $data['total_tarjetas'] = count($tarjetas);
    }
    
    $pagos = $api->get('/pagos/');
    if ($pagos) {
        $data['total_pagos'] = count($pagos);
    }
    
    $controladores = $api->get('/controladores/');
    if ($controladores) {
        $data['total_controladores'] = count($controladores);
    }
    
    // Obtener registros recientes (últimos 5)
    $registros = $api->get('/registros/', ['limit' => 5]);
    if ($registros) {
        $data['registros_recientes'] = $registros;
    }
    
    // Obtener pagos pendientes o vencidos (últimos 5)
    $pagosPendientes = $api->get('/pagos/', ['limit' => 5, 'estado' => 'PENDIENTE,VENCIDO']);
    if ($pagosPendientes) {
        $data['pagos_pendientes'] = $pagosPendientes;
    }
    
    return $data;
}
