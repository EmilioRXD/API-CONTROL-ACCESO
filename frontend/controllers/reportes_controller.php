<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/api_client.php';

/**
 * Obtiene datos para el reporte de estudiantes
 * 
 * @param array $filtros Filtros para el reporte
 * @return array Datos para el reporte
 */
function getReporteEstudiantes($filtros = []) {
    $api = new ApiClient();
    
    // Obtener todos los estudiantes sin filtros
    $estudiantes = $api->get('/estudiantes/', ['limit' => 1000]) ?: [];
    
    // Para depuración
    file_put_contents(__DIR__ . '/../debug_estudiantes.txt', 
        "Generando reporte de estudiantes\n" .
        "Total de estudiantes: " . count($estudiantes) . "\n" .
        "Datos: " . print_r($estudiantes, true) . "\n"
    );
    
    // Filtrar localmente si es necesario
    if (isset($filtros['carrera_id']) && !empty($filtros['carrera_id'])) {
        $carrera_id = $filtros['carrera_id'];
        $estudiantes = array_filter($estudiantes, function($estudiante) use ($carrera_id) {
            return isset($estudiante['carrera_id']) && $estudiante['carrera_id'] == $carrera_id;
        });
        $estudiantes = array_values($estudiantes); // Reindexar array
    }
    
    // Procesar los estudiantes para el reporte
    $reporteData = [];
    foreach ($estudiantes as $estudiante) {
        // La API ahora devuelve nombre_carrera directamente en el objeto estudiante
        $carreraNombre = 'N/A'; // valor por defecto
        
        // Usar el campo nombre_carrera que ahora viene en la respuesta de la API
        if (isset($estudiante['nombre_carrera'])) {
            $carreraNombre = $estudiante['nombre_carrera'];
        }
        
        // Crear entrada para el reporte con la estructura actualizada
        $reporteData[] = [
            'cedula' => isset($estudiante['cedula']) ? $estudiante['cedula'] : 'N/A',
            'nombre' => isset($estudiante['nombre']) ? $estudiante['nombre'] : 'N/A',
            'apellido' => isset($estudiante['apellido']) ? $estudiante['apellido'] : 'N/A',
            'carrera' => $carreraNombre,
            'carrera_nombre' => $carreraNombre // mantener para compatibilidad
        ];
    }
    
    // Para depuración
    file_put_contents(__DIR__ . '/../debug_estudiantes.txt', 
        file_get_contents(__DIR__ . '/../debug_estudiantes.txt') .
        "Datos procesados: " . count($reporteData) . "\n", 
        FILE_APPEND
    );
    
    return $reporteData;
}

/**
 * Obtiene datos para el reporte de estudiantes con tarjetas
 * 
 * @param array $filtros Filtros para el reporte
 * @return array Datos para el reporte
 */
function getReporteEstudiantesTarjetas($filtros = []) {
    $api = new ApiClient();
    
    // Obtener todos los estudiantes
    $params = [
        'limit' => isset($filtros['limit']) ? $filtros['limit'] : 1000
    ];
    
    if (isset($filtros['carrera_id']) && !empty($filtros['carrera_id'])) {
        $params['carrera_id'] = $filtros['carrera_id'];
    }
    
    $estudiantes = $api->get('/estudiantes/', $params) ?: [];
    
    // Obtener todas las tarjetas activas
    $tarjetas = $api->get('/tarjetas/', ['activa' => 'true']) ?: [];
    
    // Crear un mapa de cedulas de estudiantes con tarjetas
    $estudiantesConTarjeta = [];
    foreach ($tarjetas as $tarjeta) {
        if (isset($tarjeta['estudiante_cedula'])) {
            $estudiantesConTarjeta[$tarjeta['estudiante_cedula']] = true;
        }
    }
    
    // Preparar datos del reporte
    $reporteData = [];
    foreach ($estudiantes as $estudiante) {
        $tieneTarjeta = isset($estudiantesConTarjeta[$estudiante['cedula']]) ? true : false;
        
        // Si el filtro de tarjeta está especificado, filtrar según corresponda
        if (isset($filtros['tiene_tarjeta'])) {
            if (($filtros['tiene_tarjeta'] === '1' && !$tieneTarjeta) || 
                ($filtros['tiene_tarjeta'] === '0' && $tieneTarjeta)) {
                continue; // Saltar este estudiante si no cumple con el filtro
            }
        }
        
        // Obtener el nombre de la carrera
        $carreraNombre = '';
        if (isset($estudiante['carrera']) && isset($estudiante['carrera']['nombre'])) {
            $carreraNombre = $estudiante['carrera']['nombre'];
        }
        
        // Crear entrada para el reporte
        $reporteData[] = [
            'cedula' => $estudiante['cedula'],
            'nombre' => $estudiante['nombre'],
            'apellido' => $estudiante['apellido'],
            'carrera' => $carreraNombre,
            'tiene_tarjeta' => $tieneTarjeta
        ];
    }
    
    return $reporteData;
}

/**
 * Obtiene datos para el reporte de tarjetas
 * 
 * @param array $filtros Filtros para el reporte
 * @return array Datos para el reporte
 */
function getReporteTarjetas($filtros = []) {
    $api = new ApiClient();
    
    // Construir parámetros de filtrado
    $params = [
        'limit' => isset($filtros['limit']) ? $filtros['limit'] : 1000
    ];
    
    if (isset($filtros['activa']) && $filtros['activa'] !== '') {
        $params['activa'] = $filtros['activa'] === '1' ? 'true' : 'false';
    }
    
    // Obtener tarjetas
    $tarjetas = $api->get('/tarjetas/', $params);
    return $tarjetas ?: [];
}

/**
 * Obtiene datos para el reporte de pagos
 * 
 * @param array $filtros Filtros para el reporte
 * @return array Datos para el reporte
 */
function getReportePagos($filtros = []) {
    $api = new ApiClient();
    
    // Construir parámetros de filtrado
    $params = [
        'limit' => isset($filtros['limit']) ? $filtros['limit'] : 1000
    ];
    
    if (isset($filtros['estado']) && !empty($filtros['estado'])) {
        $params['estado'] = $filtros['estado'];
    }
    
    if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
        $params['fecha_inicio'] = $filtros['fecha_inicio'];
    }
    
    if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
        $params['fecha_fin'] = $filtros['fecha_fin'];
    }
    
    // Obtener pagos
    $pagos = $api->get('/pagos/', $params);
    return $pagos ?: [];
}

/**
 * Obtiene datos para el reporte de registros de acceso
 * 
 * @param array $filtros Filtros para el reporte
 * @return array Datos para el reporte
 */
function getReporteRegistros($filtros = []) {
    // Incluir funciones de filtrado local
    require_once __DIR__ . '/../helpers/filtros.php';
    
    $api = new ApiClient();
    
    // Obtener todos los registros (sin filtros en la API)
    $todos_registros = $api->get('/registros/', ['limit' => 1000]);
    $todos_registros = $todos_registros ?: [];
    
    // Aplicar filtros localmente
    $registros_filtrados = aplicarFiltrosLocalmente($todos_registros, $filtros);
    
    // Para depuración
    file_put_contents(__DIR__ . '/../debug_reportes.txt', 
        "Generando reporte de registros\n" .
        "Total de registros: " . count($todos_registros) . "\n" .
        "Filtros: " . print_r($filtros, true) . "\n" .
        "Registros filtrados: " . count($registros_filtrados) . "\n"
    );
    
    return $registros_filtrados;
}

/**
 * Obtiene datos para un comprobante de pago específico
 * 
 * @param int $pago_id ID del pago
 * @return array|null Datos del comprobante o null si no existe
 */
function getComprobantePago($pago_id) {
    $api = new ApiClient();
    
    // Obtener pago
    $pago = $api->get('/pagos/' . $pago_id);
    
    if (!$pago) {
        return null;
    }
    
    // Obtener datos del estudiante
    $estudiante = null;
    if (isset($pago['estudiante_cedula'])) {
        $estudiante = $api->get('/estudiantes/' . $pago['estudiante_cedula']);
    }
    
    // Obtener datos de la cuota
    $cuota = null;
    if (isset($pago['cuota_id'])) {
        $cuota = $api->get('/cuotas/' . $pago['cuota_id']);
    }
    
    return [
        'pago' => $pago,
        'estudiante' => $estudiante,
        'cuota' => $cuota
    ];
}

/**
 * Genera un reporte en PDF
 * 
 * @param string $titulo Título del reporte
 * @param array $cabeceras Cabeceras de columnas
 * @param array $datos Datos del reporte
 * @param array $columnas Columnas a incluir en el reporte
 * @return string Contenido del PDF
 */
function generarPDF($titulo, $cabeceras, $datos, $columnas) {
    // Verificar que tenemos datos
    if (empty($datos)) {
        // Crear un PDF que indique que no hay datos disponibles
        require_once __DIR__ . '/../lib/tcpdf/tcpdf.php';
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Sistema de Control de Acceso');
        $pdf->SetAuthor('Administrador');
        $pdf->SetTitle('No hay datos disponibles');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'No hay datos disponibles para mostrar en el reporte.', 0, 1, 'C');
        return $pdf->Output('', 'S');
    }
    
    // Si tenemos datos, procedemos normalmente
    // Depuración de los datos recibidos
    file_put_contents(__DIR__ . '/../debug_pdf.txt', 
        "=== GENERANDO PDF ===\n" .
        "Título: {$titulo}\n" .
        "Cabeceras: " . print_r($cabeceras, true) . "\n" .
        "Columnas: " . print_r($columnas, true) . "\n" .
        "Datos: " . print_r($datos, true) . "\n" .
        "Cantidad de datos: " . count($datos) . "\n"
    );
    // Incluir la biblioteca TCPDF con verificación
    $tcpdf_path = __DIR__ . '/../lib/tcpdf/tcpdf.php';
    
    if (!file_exists($tcpdf_path)) {
        die('Error: TCPDF library not found at ' . $tcpdf_path);
    }
    
    require_once $tcpdf_path;
    
    if (!class_exists('TCPDF')) {
        die('Error: TCPDF class not found after including ' . $tcpdf_path);
    }
    
    // Crear una nueva instancia de TCPDF con formato carta
    $pdf = new TCPDF('L', 'mm', 'LETTER', true, 'UTF-8', false);
    
    // Establecer información del documento
    $pdf->SetCreator('Sistema de Control de Acceso y Pagos');
    $pdf->SetAuthor('Universidad Tecnológica');
    $pdf->SetTitle($titulo);
    $pdf->SetSubject($titulo);
    $pdf->SetKeywords('Universidad, Sistema, Control, Acceso, Pagos, Reporte');
    
    // Eliminar cabecera y pie de página predeterminados
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true); // Activamos el pie de página
    $pdf->setFooterFont(Array('helvetica', 'I', 8));
    $pdf->setFooterData(array(0,0,0), array(0,0,0));
    $pdf->SetFooterMargin(10);
    
    // Establecer márgenes - Aumentamos el margen superior para el encabezado
    $pdf->SetMargins(10, 30, 10);
    
    // Establecer auto salto de página
    $pdf->SetAutoPageBreak(true, 25);
    
    // Agregar una página
    $pdf->AddPage();
    
    // -- ENCABEZADO PERSONALIZADO CON LOGO --
    
    // Comprobar si existe el logo universitario
    $logoPath = __DIR__ . '/../assets/img/universidad_logo.png';
    $logoExists = file_exists($logoPath);
    
    // Establecer colores institucionales
    $pdf->SetFillColor(0, 51, 153); // Azul institucional
    $pdf->SetTextColor(0, 0, 0); // Texto negro
    
    // Rectángulo para el encabezado
    $pdf->Rect(10, 10, $pdf->getPageWidth() - 20, 18, 'F');
    
    // Si existe el logo, lo añadimos
    if ($logoExists) {
        $pdf->Image($logoPath, 15, 11, 16); // Añadir logo pequeño a la izquierda
    }
    
    // Nombre de la universidad en blanco sobre fondo azul
    $pdf->SetTextColor(255, 255, 255); // Texto blanco
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetXY(35, 10);
    $pdf->Cell(100, 9, 'UNIVERSIDAD TECNOLÓGICA', 0, 0, 'L');
    
    // Subtítulo o lema
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetXY(35, 18);
    $pdf->Cell(100, 5, 'Sistema de Control de Acceso y Pagos', 0, 0, 'L');
    
    // Título del reporte (al lado derecho del encabezado)
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY($pdf->getPageWidth() - 110, 14);
    $pdf->Cell(100, 6, $titulo, 0, 0, 'R');
    
    // Restaurar color de texto
    $pdf->SetTextColor(0, 0, 0);
    
    // Agregar fecha de generación bajo el encabezado
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetXY(10, 30);
    $pdf->Cell(0, 6, 'Fecha de generación: ' . date('Y-m-d H:i:s'), 0, 1, 'R');
    
    // Espacio después del encabezado
    $pdf->Ln(5);
    
    // Crear tabla
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Calcular el ancho de las columnas (distribuido equitativamente)
    $numColumnas = count($cabeceras);
    $anchoPagina = $pdf->getPageWidth() - 20; // 20 es la suma de los márgenes izquierdo y derecho
    $anchoColumna = $anchoPagina / $numColumnas;
    
    // Crear array de anchos para cada columna
    $w = array_fill(0, $numColumnas, $anchoColumna);
    
    // Cabeceras de la tabla
    $pdf->SetFillColor(230, 230, 230);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(128, 128, 128);
    $pdf->SetLineWidth(0.3);
    
    // Agregar cabeceras
    foreach ($cabeceras as $i => $cabecera) {
        $pdf->Cell($w[$i], 7, $cabecera, 1, 0, 'C', 1);
    }
    $pdf->Ln();
    
    // Agregar datos
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetFillColor(255, 255, 255);
    $fill = false;
    
    // Imprimir los datos en el log para depuración
    file_put_contents(__DIR__ . '/../debug_pdf.txt', file_get_contents(__DIR__ . '/../debug_pdf.txt') . "\nProcesando filas de datos para la tabla... \n", FILE_APPEND);
    
    foreach ($datos as $index => $fila) {
        // Depuración
        file_put_contents(__DIR__ . '/../debug_pdf.txt', file_get_contents(__DIR__ . '/../debug_pdf.txt') . "Fila {$index}: " . print_r($fila, true) . "\n", FILE_APPEND);
        
        foreach ($columnas as $i => $columna) {
            // Manejar campos anidados como carrera_nombre
            if ($columna === 'carrera_nombre') {
                if (isset($fila['carrera']) && isset($fila['carrera']['nombre'])) {
                    $valor = $fila['carrera']['nombre'];
                } elseif (isset($fila['carrera']) && is_string($fila['carrera'])) {
                    $valor = $fila['carrera']; // En caso de que carrera sea un string
                } else {
                    $valor = 'N/A';
                }
            } else {
                // Obtener el valor de la columna
                $valor = isset($fila[$columna]) ? $fila[$columna] : 'N/A';
                
                // Depuración
                file_put_contents(__DIR__ . '/../debug_pdf.txt', file_get_contents(__DIR__ . '/../debug_pdf.txt') . "  Columna {$columna}: {$valor}\n", FILE_APPEND);
            }
            
            // Formatear valores especiales
            if (is_bool($valor) || $columna === 'tiene_tarjeta' || $columna === 'acceso_permitido') {
                // Para campos booleanos
                if ($valor === true || $valor === 1 || $valor === '1' || $valor === 'true') {
                    $valor = 'Sí';
                } else if ($valor === false || $valor === 0 || $valor === '0' || $valor === 'false') {
                    $valor = 'No';
                }
            } elseif ($columna === 'estado') {
                switch ($valor) {
                    case 'PENDIENTE':
                        $valor = 'Pendiente';
                        break;
                    case 'PAGADO':
                        $valor = 'Pagado';
                        break;
                    case 'VENCIDO':
                        $valor = 'Vencido';
                        break;
                }
            } elseif ($columna === 'fecha_hora' || $columna === 'fecha_creacion' || $columna === 'fecha_pago') {
                // Dar formato a las fechas
                if (!empty($valor) && $valor !== 'N/A' && strtotime($valor)) {
                    $fecha = new DateTime($valor);
                    $valor = $fecha->format('d/m/Y H:i:s');
                }
            }
            
            // Convertir a string si es necesario
            $valor = is_array($valor) || is_object($valor) ? json_encode($valor) : (string)$valor;
            
            // Limitar el ancho de las celdas y permitir múltiples líneas
            $pdf->Cell($w[$i], 6, $valor, 1, 0, 'L', $fill);
        }
        $pdf->Ln();
        $fill = !$fill; // Alternar el color de fondo
    }
    
    // Agregar pie de página
    $pdf->SetY(-15);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 10, 'Este reporte fue generado desde el Sistema de Control de Acceso y Pagos.', 0, 0, 'C');
    
    // Devolver el PDF como string
    return $pdf->Output('', 'S');
}

/**
 * Genera un reporte en Excel (CSV)
 * 
 * @param string $titulo Título del reporte
 * @param array $cabeceras Cabeceras de columnas
 * @param array $datos Datos del reporte
 * @param array $columnas Columnas a incluir en el reporte
 * @return string Contenido del CSV
 */
function generarExcel($titulo, $cabeceras, $datos, $columnas) {
    // Inicializar buffer de salida
    $output = fopen('php://temp', 'r+');
    
    // Agregar BOM (Byte Order Mark) para UTF-8
    fputs($output, "\xEF\xBB\xBF");
    
    // Agregar cabeceras
    fputcsv($output, $cabeceras);
    
    // Agregar datos
    foreach ($datos as $fila) {
        $linea = [];
        foreach ($columnas as $columna) {
            $valor = isset($fila[$columna]) ? $fila[$columna] : '';
            
            // Formatear valores especiales
            if (is_bool($valor)) {
                $valor = $valor ? 'Sí' : 'No';
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

/**
 * Genera un comprobante de pago en PDF
 * 
 * @param array $datos Datos del comprobante
 * @return string Contenido del PDF
 */
function generarComprobantePDF($datos) {
    $pago = $datos['pago'];
    $estudiante = $datos['estudiante'];
    $cuota = $datos['cuota'];
    
    // Incluir la biblioteca TCPDF con verificación
    $tcpdf_path = __DIR__ . '/../lib/tcpdf/tcpdf.php';
    
    if (!file_exists($tcpdf_path)) {
        die('Error: TCPDF library not found at ' . $tcpdf_path);
    }
    
    require_once $tcpdf_path;
    
    if (!class_exists('TCPDF')) {
        die('Error: TCPDF class not found after including ' . $tcpdf_path);
    }
    
    // Crear una nueva instancia de TCPDF con formato carta
    $pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
    
    // Establecer información del documento
    $pdf->SetCreator('Sistema de Control de Acceso y Pagos');
    $pdf->SetAuthor('Sistema de Control de Acceso y Pagos');
    $pdf->SetTitle('Comprobante de Pago');
    $pdf->SetSubject('Comprobante de Pago');
    $pdf->SetKeywords('Comprobante, Pago, Sistema, Control, Acceso');
    
    // Eliminar cabecera y pie de página predeterminados
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Establecer márgenes
    $pdf->SetMargins(15, 15, 15);
    
    // Establecer auto salto de página
    $pdf->SetAutoPageBreak(true, 15);
    
    // Agregar una página
    $pdf->AddPage();
    
    // Título y encabezado
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'COMPROBANTE DE PAGO', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 6, 'Sistema de Control de Acceso y Pagos', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Detalles del comprobante
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(90, 7, 'DATOS DEL COMPROBANTE', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    
    // Información del pago
    $pdf->Cell(50, 7, 'N° de Comprobante:', 0, 0, 'L');
    $pdf->Cell(100, 7, $pago['id'], 0, 1, 'L');
    
    $pdf->Cell(50, 7, 'Fecha:', 0, 0, 'L');
    $pdf->Cell(100, 7, $pago['fecha_creacion'], 0, 1, 'L');
    
    if (isset($pago['fecha_pago'])) {
        $pdf->Cell(50, 7, 'Fecha de Pago:', 0, 0, 'L');
        $pdf->Cell(100, 7, $pago['fecha_pago'], 0, 1, 'L');
    }
    
    $pdf->Cell(50, 7, 'Estado:', 0, 0, 'L');
    $pdf->Cell(100, 7, ($pago['estado'] === 'PAGADO' ? 'PAGADO' : 'PENDIENTE'), 0, 1, 'L');
    
    $pdf->Ln(5);
    
    // Datos del estudiante
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(90, 7, 'DATOS DEL ESTUDIANTE', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    
    $nombreEstudiante = isset($estudiante['nombre']) && isset($estudiante['apellido']) ? 
                        $estudiante['nombre'] . ' ' . $estudiante['apellido'] : 
                        $pago['estudiante_cedula'];
    
    $pdf->Cell(50, 7, 'Estudiante:', 0, 0, 'L');
    $pdf->Cell(100, 7, $nombreEstudiante, 0, 1, 'L');
    
    $pdf->Cell(50, 7, 'Cédula:', 0, 0, 'L');
    $pdf->Cell(100, 7, $pago['estudiante_cedula'], 0, 1, 'L');
    
    if (isset($estudiante['carrera']) && isset($estudiante['carrera']['nombre'])) {
        $pdf->Cell(50, 7, 'Carrera:', 0, 0, 'L');
        $pdf->Cell(100, 7, $estudiante['carrera']['nombre'], 0, 1, 'L');
    }
    
    $pdf->Ln(5);
    
    // Detalles del pago
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(180, 7, 'DETALLE DEL PAGO', 0, 1, 'L');
    
    // Tabla de detalle
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(120, 7, 'Concepto', 1, 0, 'C', 1);
    $pdf->Cell(60, 7, 'Monto', 1, 1, 'C', 1);
    
    $pdf->SetFont('helvetica', '', 10);
    
    // Usar nombre_cuota directamente del pago, ya que ahora viene en la respuesta de la API
    $concepto = isset($pago['nombre_cuota']) ? $pago['nombre_cuota'] : 'Cuota';
    // Ya no se muestra el monto en las cuotas
    $monto = 'N/A';
    
    // Si hay fecha de vencimiento, agregarla al concepto
    if (isset($pago['fecha_vencimiento'])) {
        $fecha_formateada = date('d/m/Y', strtotime($pago['fecha_vencimiento']));
        $concepto .= ' (Vence: ' . $fecha_formateada . ')';
    }
    
    $pdf->Cell(120, 7, $concepto, 1, 0, 'L');
    $pdf->Cell(60, 7, $monto, 1, 1, 'R');
    
    // Total
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(120, 7, 'Total:', 0, 0, 'R');
    $pdf->Cell(60, 7, $monto, 0, 1, 'R');
    
    // Sello de PAGADO si corresponde
    if ($pago['estado'] === 'PAGADO') {
        $pdf->Ln(10);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->Cell(0, 10, 'PAGADO', 1, 0, 'C');
        $pdf->SetTextColor(0, 0, 0);
    }
    
    // Pie de página
    $pdf->SetY(-30);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 5, 'Este comprobante fue generado desde el Sistema de Control de Acceso y Pagos.', 0, 1, 'C');
    $pdf->Cell(0, 5, 'El presente documento es válido como comprobante de pago.', 0, 1, 'C');
    
    // Devolver el PDF como string
    return $pdf->Output('', 'S');
}
