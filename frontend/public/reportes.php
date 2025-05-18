<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../controllers/reportes_controller.php';
require_once __DIR__ . '/../controllers/controladores_controller.php';
require_once __DIR__ . '/../controllers/estudiantes_controller.php';

// Verificar autenticación
requireAuth();

// Obtener la acción del query string
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Procesar la acción
switch ($action) {
    case 'index':
        // Mostrar la barra de navegación
        $showNavbar = true;
        
        // Obtener listas para los filtros
        $carreras = getCarreras();
        $controladores = getControladores();
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario de reportes
        require_once __DIR__ . '/../views/reportes/form.php';
        
        // Incluir la plantilla de pie de página
        require_once __DIR__ . '/../views/templates/footer.php';
        break;
        
    case 'generar':
        // Verificar que se ha especificado un tipo de reporte
        if (!isset($_GET['tipo_reporte']) || empty($_GET['tipo_reporte'])) {
            $_SESSION['flash_message'] = 'Debe especificar un tipo de reporte.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/reportes.php');
            exit;
        }
        
        // Obtener parámetros
        $tipo_reporte = $_GET['tipo_reporte'];
        $formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
        
        // Generar reporte según el tipo
        switch ($tipo_reporte) {
            case 'estudiantes':
                // Filtros para estudiantes
                $filtros = [
                    'carrera_id' => isset($_GET['carrera_id']) ? $_GET['carrera_id'] : null,
                    'limit' => isset($_GET['limit']) ? $_GET['limit'] : 100
                ];
                
                // Obtener datos
                $datos = getReporteEstudiantes($filtros);
                
                // Definir cabeceras y columnas
                $titulo = 'Reporte de Estudiantes';
                $cabeceras = ['Cédula', 'Nombre', 'Apellido', 'Carrera'];
                $columnas = ['cedula', 'nombre', 'apellido', 'carrera'];
                
                break;
                
            case 'estudiantes_tarjetas':
                // Filtros para estudiantes con tarjetas
                $filtros = [
                    'carrera_id' => isset($_GET['carrera_id']) ? $_GET['carrera_id'] : null,
                    'tiene_tarjeta' => isset($_GET['tiene_tarjeta']) ? $_GET['tiene_tarjeta'] : null,
                    'limit' => isset($_GET['limit']) ? $_GET['limit'] : 100
                ];
                
                // Obtener datos
                $datos = getReporteEstudiantesTarjetas($filtros);
                
                // Definir cabeceras y columnas
                $titulo = 'Reporte de Estudiantes con Tarjetas';
                $cabeceras = ['Cédula', 'Nombre', 'Apellido', 'Carrera', 'Tarjeta'];
                $columnas = ['cedula', 'nombre', 'apellido', 'carrera', 'tiene_tarjeta'];
                
                break;
                
            case 'tarjetas':
                // Filtros para tarjetas
                $filtros = [
                    'activa' => isset($_GET['activa']) ? $_GET['activa'] : null,
                    'limit' => isset($_GET['limit']) ? $_GET['limit'] : 100
                ];
                
                // Obtener datos
                $datos = getReporteTarjetas($filtros);
                
                // Definir cabeceras y columnas
                $titulo = 'Reporte de Tarjetas';
                $cabeceras = ['ID', 'Serial', 'Estudiante', 'Fecha Emisión', 'Fecha Expiración', 'Estado'];
                $columnas = ['id', 'serial', 'estudiante_cedula', 'fecha_emision', 'fecha_expiracion', 'activa'];
                
                break;
                
            case 'pagos':
                // Filtros para pagos
                $filtros = [
                    'estado' => isset($_GET['estado']) ? $_GET['estado'] : null,
                    'fecha_inicio' => isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null,
                    'fecha_fin' => isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null,
                    'limit' => isset($_GET['limit']) ? $_GET['limit'] : 100
                ];
                
                // Obtener datos
                $datos = getReportePagos($filtros);
                
                // Definir cabeceras y columnas
                $titulo = 'Reporte de Pagos';
                $cabeceras = ['ID', 'Estudiante', 'Cuota', 'Estado', 'Fecha Creación', 'Fecha Pago'];
                $columnas = ['id', 'estudiante_cedula', 'cuota_id', 'estado', 'fecha_creacion', 'fecha_pago'];
                
                break;
                
            case 'registros':
                // Filtros para registros
                $filtros = [
                    'tipo' => isset($_GET['tipo']) ? $_GET['tipo'] : null,
                    'controlador_id' => isset($_GET['controlador_id']) ? $_GET['controlador_id'] : null,
                    'fecha_inicio' => isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null,
                    'fecha_fin' => isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null,
                    'limit' => isset($_GET['limit']) ? $_GET['limit'] : 100
                ];
                
                // Obtener datos
                $datos = getReporteRegistros($filtros);
                
                // Definir cabeceras y columnas
                $titulo = 'Reporte de Registros de Acceso';
                $cabeceras = ['ID', 'Tarjeta', 'Ubicación', 'Fecha/Hora', 'Acceso'];
                $columnas = ['id', 'id_tarjeta', 'ubicacion_controlador', 'fecha_hora', 'acceso_permitido'];
                
                break;
                
            default:
                $_SESSION['flash_message'] = 'Tipo de reporte no válido.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . URL_BASE . '/public/reportes.php');
                exit;
        }
        
        // Generar contenido según el formato
        if ($formato === 'pdf') {
            $contenido = generarPDF($titulo, $cabeceras, $datos, $columnas);
            $contentType = 'application/pdf';
            $extension = 'pdf';
        } else {
            $contenido = generarExcel($titulo, $cabeceras, $datos, $columnas);
            $contentType = 'text/csv';
            $extension = 'csv';
        }
        
        // Enviar respuesta al navegador
        header('Content-Type: ' . $contentType . '; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_' . $tipo_reporte . '_' . date('Y-m-d') . '.' . $extension . '"');
        echo $contenido;
        exit;
        
    case 'generar_comprobante':
        // Verificar que se ha especificado un ID de pago
        if (!isset($_GET['pago_id']) || empty($_GET['pago_id'])) {
            $_SESSION['flash_message'] = 'Debe especificar un ID de pago.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/pagos.php');
            exit;
        }
        
        // Obtener ID del pago
        $pago_id = filter_input(INPUT_GET, 'pago_id', FILTER_SANITIZE_NUMBER_INT);
        
        // Obtener datos del comprobante
        $datos = getComprobantePago($pago_id);
        
        if (!$datos || !isset($datos['pago'])) {
            $_SESSION['flash_message'] = 'No se pudo generar el comprobante. Pago no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/pagos.php');
            exit;
        }
        
        // Generar comprobante
        $contenido = generarComprobantePDF($datos);
        
        // Enviar respuesta al navegador
        header('Content-Type: application/pdf; charset=utf-8');
        header('Content-Disposition: attachment; filename="comprobante_pago_' . $pago_id . '.pdf"');
        echo $contenido;
        exit;
        
    default:
        // Acción desconocida, redirigir a la página de reportes
        header('Location: ' . URL_BASE . '/public/reportes.php');
        exit;
}
?>
