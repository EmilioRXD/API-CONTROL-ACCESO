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
        'total_tarjetas' => 0,
        'total_controladores' => 0,
        'total_accesos' => 0,
        'total_accesos_denegados' => 0,
        'registros_recientes' => []
    ];
    
    // Obtener totales
    $tarjetas = $api->get('/tarjetas/');
    if ($tarjetas) {
        $data['total_tarjetas'] = count($tarjetas);
    }
    
    $controladores = $api->get('/controladores/');
    if ($controladores) {
        $data['total_controladores'] = count($controladores);
    }
    
    // Obtener todos los registros para filtrar localmente
    $todos_registros = $api->get('/registros/', ['limit' => 100]); 
    
    if ($todos_registros) {
        // Filtrar accesos permitidos - ahora usando 'acceso_permitido' (true)
        $accesos_permitidos = array_filter($todos_registros, function($registro) {
            return isset($registro['acceso_permitido']) && $registro['acceso_permitido'] === true;
        });
        $data['total_accesos'] = count($accesos_permitidos);
        
        // Filtrar accesos denegados - ahora usando 'acceso_permitido' (false)
        $accesos_denegados = array_filter($todos_registros, function($registro) {
            return isset($registro['acceso_permitido']) && $registro['acceso_permitido'] === false;
        });
        $data['total_accesos_denegados'] = count($accesos_denegados);
        
        // Obtener datos adicionales de tarjetas y controladores
        $tarjetas = $api->get('/tarjetas/');
        $controladores = $api->get('/controladores/');
        $tarjetas_map = [];
        $controladores_map = [];
        
        if ($tarjetas) {
            foreach ($tarjetas as $tarjeta) {
                if (isset($tarjeta['id'])) {
                    $tarjetas_map[$tarjeta['id']] = $tarjeta;
                }
            }
        }
        
        if ($controladores) {
            foreach ($controladores as $controlador) {
                if (isset($controlador['id'])) {
                    $controladores_map[$controlador['id']] = $controlador;
                }
            }
        }
        
        // Enriquecer los registros con datos de tarjeta y controlador
        foreach ($accesos_denegados as &$registro) {
            if (isset($registro['id_tarjeta']) && isset($tarjetas_map[$registro['id_tarjeta']])) {
                $registro['info_tarjeta'] = $tarjetas_map[$registro['id_tarjeta']];
                // Agregar info del estudiante si existe en la tarjeta
                if (isset($tarjetas_map[$registro['id_tarjeta']]['estudiante'])) {
                    $registro['info_estudiante'] = $tarjetas_map[$registro['id_tarjeta']]['estudiante'];
                }
            }
            
            if (isset($registro['id_controlador']) && isset($controladores_map[$registro['id_controlador']])) {
                $registro['info_controlador'] = $controladores_map[$registro['id_controlador']];
            }
        }
        
        // Aplicar el mismo enriquecimiento a registros recientes
        foreach ($accesos_permitidos as &$registro) {
            if (isset($registro['id_tarjeta']) && isset($tarjetas_map[$registro['id_tarjeta']])) {
                $registro['info_tarjeta'] = $tarjetas_map[$registro['id_tarjeta']];
                if (isset($tarjetas_map[$registro['id_tarjeta']]['estudiante'])) {
                    $registro['info_estudiante'] = $tarjetas_map[$registro['id_tarjeta']]['estudiante'];
                }
            }
            
            if (isset($registro['id_controlador']) && isset($controladores_map[$registro['id_controlador']])) {
                $registro['info_controlador'] = $controladores_map[$registro['id_controlador']];
            }
        }
        
        // Filtrar registros de hoy
        $fechaHoy = date('Y-m-d');
        $registros_hoy = array_filter($todos_registros, function($registro) use ($fechaHoy) {
            // Extraer solo la parte de la fecha (sin la hora)
            $fechaRegistro = substr($registro['fecha_hora'], 0, 10);
            return $fechaRegistro === $fechaHoy;
        });
        
        // Guardar los accesos denegados y los registros de hoy para usar en la vista
        $data['accesos_denegados'] = array_values($accesos_denegados);
        $data['registros_recientes'] = array_values($registros_hoy);
    } else {
        $data['total_accesos'] = 0;
        $data['total_accesos_denegados'] = 0;
        $data['accesos_denegados'] = [];
    }
    
    // Obtener registros y filtrar manualmente los de hoy
    $fechaHoy = date('Y-m-d');
    // Obtenemos más registros para asegurarnos de tener suficientes después de filtrar
    $registros = $api->get('/registros/', ['limit' => 50]);
    if ($registros) {
        // Filtrar solo los registros de hoy
        $registrosHoy = array_filter($registros, function($registro) use ($fechaHoy) {
            // Extraer solo la parte de la fecha (sin la hora)
            $fechaRegistro = substr($registro['fecha_hora'], 0, 10);
            return $fechaRegistro === $fechaHoy;
        });
        
        // Asignar solo los registros de hoy
        $data['registros_recientes'] = array_values($registrosHoy); // reset de índices
    }
    
    // Ya no necesitamos los pagos pendientes
    
    return $data;
}
