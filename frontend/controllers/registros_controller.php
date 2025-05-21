<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';
require_once __DIR__ . '/../helpers/filtros.php';

/**
 * Obtiene la lista de registros de acceso desde la API
 * 
 * @param int $skip Registros a omitir para paginación
 * @param int $limit Límite de registros a obtener
 * @param array $filtros Filtros adicionales (fecha_inicio, fecha_fin, acceso_permitido, ubicacion_controlador)
 * @param string $sort Campo por el que ordenar
 * @param string $order Dirección de ordenamiento (asc, desc)
 * @return array Lista de registros
 */
function getRegistros($skip = 0, $limit = 20, $filtros = [], $sort = '', $order = 'asc') {
    // Incluir funciones de filtrado local
    require_once __DIR__ . '/../helpers/filtros.php';
    
    $api = new ApiClient();
    
    // Obtener todos los registros sin filtrar
    $registros = $api->get('/registros/', ['skip' => 0, 'limit' => 500]);
    
    // Si no hay registros, devolver un array vacío
    if (!$registros) {
        return [];
    }
    
    // Si es un objeto único, convertirlo a array
    if (is_object($registros) && isset($registros->id)) {
        $registros = [$registros];
    }
    
    // Aplicar filtros en PHP
    if (!empty($filtros)) {
        $registrosFiltrados = [];
        
        foreach ($registros as $registro) {
            $incluir = true;
            
            // Filtro por fecha de inicio
            if (!empty($filtros['fecha_inicio']) && isset($registro['fecha_hora'])) {
                $fechaRegistro = substr($registro['fecha_hora'], 0, 10); // Extraer YYYY-MM-DD
                if ($fechaRegistro < $filtros['fecha_inicio']) {
                    $incluir = false;
                }
            }
            
            // Filtro por fecha de fin
            if ($incluir && !empty($filtros['fecha_fin']) && isset($registro['fecha_hora'])) {
                $fechaRegistro = substr($registro['fecha_hora'], 0, 10); // Extraer YYYY-MM-DD
                if ($fechaRegistro > $filtros['fecha_fin']) {
                    $incluir = false;
                }
            }
            
            // Filtro por acceso permitido
            if ($incluir && isset($filtros['acceso_permitido']) && $filtros['acceso_permitido'] !== '' && isset($registro['acceso_permitido'])) {
                $acceso_permitido_filtro = $filtros['acceso_permitido'] === true || $filtros['acceso_permitido'] === '1' || $filtros['acceso_permitido'] === 1;
                if ($registro['acceso_permitido'] != $acceso_permitido_filtro) {
                    $incluir = false;
                }
            }
            
            // Filtro por ubicación del controlador
            if ($incluir && !empty($filtros['ubicacion_controlador']) && isset($registro['ubicacion_controlador'])) {
                if ($registro['ubicacion_controlador'] != $filtros['ubicacion_controlador']) {
                    $incluir = false;
                }
            }
            
            // Filtro por tipo de acceso (ENTRADA/SALIDA)
            if ($incluir && !empty($filtros['tipo_acceso']) && isset($registro['tipo_acceso_controlador'])) {
                if (strtoupper($registro['tipo_acceso_controlador']) != strtoupper($filtros['tipo_acceso'])) {
                    $incluir = false;
                }
            }
            
            if ($incluir) {
                $registrosFiltrados[] = $registro;
            }
        }
        
        $registros = $registrosFiltrados;
    }
    
    // Ordenar registros si se especifica
    if (!empty($sort)) {
        usort($registros, function($a, $b) use ($sort, $order) {
            if (!isset($a[$sort]) || !isset($b[$sort])) {
                return 0;
            }
            
            $cmp = strcmp($a[$sort], $b[$sort]);
            return $order === 'desc' ? -$cmp : $cmp;
        });
    } else {
        // Por defecto, ordenar por ID descendente (más recientes primero)
        usort($registros, function($a, $b) {
            return $b['id'] - $a['id'];
        });
    }
    
    // Aplicar paginación
    return array_slice($registros, $skip, $limit);
}

/**
 * Obtiene los detalles de un registro específico
 * 
 * @param int $id ID del registro
 * @return array|null Detalles del registro o null si no existe
 */
function getRegistro($id) {
    $api = new ApiClient();
    return $api->get('/registros/' . $id);
}

/**
 * Obtiene la lista de ubicaciones de controladores disponibles
 * 
 * @return array Lista de ubicaciones únicas
 */
function getUbicacionesControladores() {
    $api = new ApiClient();
    $controladores = $api->get('/controladores/');
    
    $ubicaciones = [];
    if ($controladores) {
        foreach ($controladores as $controlador) {
            if (isset($controlador['ubicacion']) && !empty($controlador['ubicacion'])) {
                $ubicaciones[$controlador['ubicacion']] = $controlador['ubicacion'];
            }
        }
    }
    
    return array_values($ubicaciones);
}

/**
 * Genera un informe de registros en formato PDF o Excel
 * 
 * @param array $filtros Filtros para el informe (fecha_inicio, fecha_fin, acceso_permitido, ubicacion_controlador)
 * @param string $formato Formato del informe (pdf, excel)
 * @return string|array Contenido del informe o datos para generar informe
 */
function generarInformeRegistros($filtros = [], $formato = 'pdf') {
    // Obtener los registros según los filtros
    $registros = getRegistros(0, 1000, $filtros);
    
    // Definir cabeceras y columnas para el informe
    $titulo = 'Informe de Registros de Acceso';
    $cabeceras = ['ID', 'ID Tarjeta', 'Ubicación', 'Fecha/Hora', 'Acceso'];
    $columnas = ['id', 'id_tarjeta', 'ubicacion_controlador', 'fecha_hora', 'acceso_permitido'];
    
    if ($formato === 'pdf') {
        // Verificar si la clase TCPDF existe
        if (!class_exists('TCPDF')) {
            // Si no existe, incluir la biblioteca
            require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';
        }
        
        // Crear instancia de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        // Configurar PDF
        $pdf->SetCreator('Sistema de Control de Acceso');
        $pdf->SetAuthor('Administrador');
        $pdf->SetTitle($titulo);
        $pdf->SetSubject('Informe de Registros de Acceso');
        $pdf->SetKeywords('registros, acceso, informe, pdf');
        
        // Configurar encabezado y pie de página
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        $pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));
        $pdf->setFooterFont(Array('helvetica', '', 8));
        $pdf->SetFooterMargin(10);
        
        // Configurar márgenes
        $pdf->SetMargins(15, 15, 15);
        
        // Agregar página
        $pdf->AddPage();
        
        // Configurar fuente
        $pdf->SetFont('helvetica', 'B', 16);
        
        // Agregar título
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
        $pdf->Ln(5);
        
        // Información de filtros
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 1);
        
        if (!empty($filtros['fecha_inicio'])) {
            $pdf->Cell(0, 6, 'Fecha inicio: ' . $filtros['fecha_inicio'], 0, 1);
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $pdf->Cell(0, 6, 'Fecha fin: ' . $filtros['fecha_fin'], 0, 1);
        }
        
        if (isset($filtros['acceso_permitido'])) {
            $acceso = $filtros['acceso_permitido'] ? 'Permitido' : 'Denegado';
            $pdf->Cell(0, 6, 'Tipo de acceso: ' . $acceso, 0, 1);
        }
        
        if (!empty($filtros['ubicacion_controlador'])) {
            $pdf->Cell(0, 6, 'Ubicación: ' . $filtros['ubicacion_controlador'], 0, 1);
        }
        
        $pdf->Ln(5);
        
        // Crear tabla
        $pdf->SetFont('helvetica', 'B', 10);
        
        // Ancho de las columnas
        $w = array(20, 30, 50, 50, 30);
        
        // Cabeceras de la tabla
        for ($i = 0; $i < count($cabeceras); $i++) {
            $pdf->Cell($w[$i], 7, $cabeceras[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Datos de la tabla
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(224, 235, 255);
        $fill = false;
        
        foreach ($registros as $registro) {
            for ($i = 0; $i < count($columnas); $i++) {
                $columna = $columnas[$i];
                $valor = isset($registro[$columna]) ? $registro[$columna] : '';
                
                // Formatear valores especiales
                if ($columna === 'acceso_permitido') {
                    $valor = $valor ? 'Permitido' : 'Denegado';
                } elseif ($columna === 'fecha_hora') {
                    // Formatear fecha si es necesario
                    if (is_string($valor) && strtotime($valor)) {
                        $fecha = new DateTime($valor);
                        $valor = $fecha->format('d/m/Y H:i:s');
                    }
                }
                
                $pdf->Cell($w[$i], 6, $valor, 1, 0, 'L', $fill);
            }
            $pdf->Ln();
            $fill = !$fill;
        }
        
        // Agregar texto final
        $pdf->Ln(5);
        $pdf->Cell(0, 6, 'Total de registros: ' . count($registros), 0, 1);
        
        // Salida del PDF
        return $pdf->Output('informe_registros.pdf', 'S');
    } else {
        // Generar contenido en formato CSV para Excel
        $output = fopen('php://temp', 'r+');
        
        // Agregar BOM (Byte Order Mark) para UTF-8
        fputs($output, "\xEF\xBB\xBF");
        
        // Agregar cabeceras
        fputcsv($output, $cabeceras);
        
        // Agregar datos
        foreach ($registros as $registro) {
            $linea = [];
            foreach ($columnas as $columna) {
                $valor = isset($registro[$columna]) ? $registro[$columna] : '';
                
                // Formatear valores especiales
                if ($columna === 'acceso_permitido') {
                    $valor = $valor ? 'Permitido' : 'Denegado';
                }
                
                $linea[] = $valor;
            }
            fputcsv($output, $linea);
        }
        
        // Obtener contenido del buffer
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}
