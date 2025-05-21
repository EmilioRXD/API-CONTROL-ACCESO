<?php
/**
 * Funciones de ayuda para la paginación de tablas
 */

/**
 * Convierte una tabla normal en una tabla paginada
 * 
 * @param string $tableId ID único para la tabla
 * @param array $data Los datos a mostrar
 * @param array $config Configuración adicional para la paginación
 * @return string HTML para inicializar la paginación
 */
function preparePaginationTable($tableId, $data, $config = []) {
    // Valores por defecto
    $defaultConfig = [
        'rowsPerPage' => 10,
        'pageSizes' => [5, 10, 25, 50, 100]
    ];
    
    // Combinar configuración
    $config = array_merge($defaultConfig, $config);
    
    // Construir el HTML para la inicialización de la paginación
    $html = '<script>
        document.addEventListener("DOMContentLoaded", function() {
            new TablePagination("' . $tableId . '", ' . json_encode($config) . ');
        });
    </script>';
    
    return $html;
}

/**
 * Renderiza una tabla paginada
 * 
 * @param string $tableId ID único para la tabla
 * @param array $data Los datos a mostrar
 * @param array $columns Definición de columnas
 * @param array $config Configuración adicional para la paginación
 * @return string HTML completo de la tabla paginada
 */
function renderPaginatedTable($tableId, $data, $columns, $config = []) {
    $html = '<div class="table-responsive">';
    $html .= '<table id="' . $tableId . '" class="table table-striped table-sm paginated-table">';
    
    // Encabezado de la tabla
    $html .= '<thead><tr>';
    foreach ($columns as $column) {
        $html .= '<th>' . htmlspecialchars($column['title']) . '</th>';
    }
    $html .= '</tr></thead>';
    
    // Cuerpo de la tabla
    $html .= '<tbody>';
    
    // Verificar si los datos son un array u objeto único
    if (is_object($data) || (is_array($data) && !isset($data[0]))) {
        $data = [$data]; // Convertir a array para procesar de manera uniforme
    }
    
    // Si no hay datos, mostrar mensaje
    if (empty($data)) {
        $html .= '<tr><td colspan="' . count($columns) . '" class="text-center">No hay datos disponibles</td></tr>';
    } else {
        // Procesar cada fila de datos
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $html .= '<td>';
                
                // Verificar si hay una función de renderizado personalizada
                if (isset($column['render']) && is_callable($column['render'])) {
                    $html .= $column['render']($row);
                } 
                // Si no, intentar obtener el valor directamente
                else if (isset($column['field']) && isset($row[$column['field']])) {
                    $html .= htmlspecialchars($row[$column['field']]);
                }
                // Valor por defecto si no hay datos
                else {
                    $html .= isset($column['default']) ? $column['default'] : 'N/A';
                }
                
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>';
    
    // Añadir script de inicialización
    $html .= preparePaginationTable($tableId, $data, $config);
    
    return $html;
}
