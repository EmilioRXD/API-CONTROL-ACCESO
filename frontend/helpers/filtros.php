<?php
/**
 * Funciones auxiliares para filtrado
 */

/**
 * Aplica filtros a un conjunto de registros localmente (PHP side)
 * Útil cuando los filtros de la API no funcionan correctamente
 * 
 * @param array $registros Array de registros a filtrar
 * @param array $filtros Filtros a aplicar
 * @return array Registros filtrados
 */
function aplicarFiltrosLocalmente($registros, $filtros) {
    if (empty($registros) || empty($filtros)) {
        return $registros;
    }
    
    $registrosFiltrados = $registros;
    
    // Filtrar por fecha de inicio
    if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
        $fechaInicio = strtotime($filtros['fecha_inicio']);
        $registrosFiltrados = array_filter($registrosFiltrados, function($registro) use ($fechaInicio) {
            return isset($registro['fecha_hora']) && strtotime($registro['fecha_hora']) >= $fechaInicio;
        });
    }
    
    // Filtrar por fecha de fin
    if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
        $fechaFin = strtotime($filtros['fecha_fin'] . ' 23:59:59'); // Incluir todo el día
        $registrosFiltrados = array_filter($registrosFiltrados, function($registro) use ($fechaFin) {
            return isset($registro['fecha_hora']) && strtotime($registro['fecha_hora']) <= $fechaFin;
        });
    }
    
    // Filtrar por acceso permitido/denegado
    if (isset($filtros['acceso_permitido']) && $filtros['acceso_permitido'] !== '') {
        $permitido = filter_var($filtros['acceso_permitido'], FILTER_VALIDATE_BOOLEAN);
        $registrosFiltrados = array_filter($registrosFiltrados, function($registro) use ($permitido) {
            return isset($registro['acceso_permitido']) && $registro['acceso_permitido'] == $permitido;
        });
    }
    
    // Filtrar por ubicación del controlador
    if (isset($filtros['ubicacion_controlador']) && !empty($filtros['ubicacion_controlador'])) {
        $ubicacion = $filtros['ubicacion_controlador'];
        $registrosFiltrados = array_filter($registrosFiltrados, function($registro) use ($ubicacion) {
            return isset($registro['ubicacion_controlador']) && $registro['ubicacion_controlador'] == $ubicacion;
        });
    }
    
    // Reindexar para que sea un array secuencial
    return array_values($registrosFiltrados);
}

/**
 * Aplica paginación a un conjunto de registros
 * 
 * @param array $registros Array de registros
 * @param int $skip Número de registros a omitir
 * @param int $limit Límite de registros a devolver
 * @return array Registros paginados
 */
function aplicarPaginacion($registros, $skip = 0, $limit = 20) {
    // Si no hay suficientes registros, devolver los que haya
    if (count($registros) <= $skip) {
        return [];
    }
    
    // Devolver los registros según paginación
    return array_slice($registros, $skip, $limit);
}
