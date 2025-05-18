<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../controllers/registros_controller.php';
require_once __DIR__ . '/../controllers/tarjetas_controller.php';
require_once __DIR__ . '/../controllers/estudiantes_controller.php';

// Verificar autenticación
requireAuth();

// Mostrar la barra de navegación
$showNavbar = true;

// Obtener la acción del query string
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Preparar URL params para paginación y mantener filtros
$url_params = '';
foreach (['fecha_inicio', 'fecha_fin', 'ubicacion_controlador'] as $param) {
    if (isset($_GET[$param]) && !empty($_GET[$param])) {
        $url_params .= '&' . $param . '=' . urlencode($_GET[$param]);
    }
}

// Manejar específicamente acceso_permitido ya que puede ser '0' (que es falso en empty)
if (isset($_GET['acceso_permitido']) && $_GET['acceso_permitido'] !== '') {
    $url_params .= '&acceso_permitido=' . urlencode($_GET['acceso_permitido']);
}

// Procesar la acción
switch ($action) {
    case 'index':
        // Obtener parámetros de paginación
        $skip = isset($_GET['skip']) ? (int)$_GET['skip'] : 0;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        
        // Obtener parámetros de ordenamiento
        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
        $order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';
        
        // Preparar filtros principales
        $filtros = [
            'fecha_inicio' => isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null,
            'fecha_fin' => isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null,
            'ubicacion_controlador' => isset($_GET['ubicacion_controlador']) ? $_GET['ubicacion_controlador'] : null
        ];
        
        // Filtro de acceso permitido (asegurarse de que sea un booleano para la API)
        if (isset($_GET['acceso_permitido']) && $_GET['acceso_permitido'] !== '') {
            $filtros['acceso_permitido'] = $_GET['acceso_permitido'] === '1' ? true : false;
        }
        
        // Obtener ubicaciones de controladores para el filtro
        $ubicaciones = getUbicacionesControladores();
        
        // Obtener registros con filtros, ordenamiento y paginación
        $registros = getRegistros($skip, $limit, $filtros, $sort, $order);
        
        // Obtener el total de registros para información de paginación
        $totalRegistros = count($registros);
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista de listado de registros
        require_once __DIR__ . '/../views/registros/list.php';
        break;
        
    case 'view':
        // Ver detalles de un registro
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de registro no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/registros.php');
            exit;
        }
        
        // Obtener datos del registro
        $registro = getRegistro($id);
        
        if (!$registro) {
            $_SESSION['flash_message'] = 'Registro no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/registros.php');
            exit;
        }
        
        // Intentar obtener información adicional de la tarjeta y el estudiante
        $tarjeta = null;
        $estudiante = null;
        
        if (isset($registro['id_tarjeta'])) {
            $tarjeta = getTarjeta($registro['id_tarjeta']);
            
            // Si hay tarjeta y tiene estudiante, obtener datos del estudiante
            if ($tarjeta && isset($tarjeta['estudiante_cedula'])) {
                $estudiante = getEstudiante($tarjeta['estudiante_cedula']);
            }
        }
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista detallada del registro
        require_once __DIR__ . '/../views/registros/view.php';
        break;
        
    case 'exportar':
        // Exportar registros en formato PDF o Excel
        $formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
        
        // Preparar filtros
        $filtros = [
            'fecha_inicio' => isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null,
            'fecha_fin' => isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null,
            'ubicacion_controlador' => isset($_GET['ubicacion_controlador']) ? $_GET['ubicacion_controlador'] : null
        ];
        
        // Filtro de acceso permitido (asegurarse de que sea un booleano para la API)
        if (isset($_GET['acceso_permitido']) && $_GET['acceso_permitido'] !== '') {
            $filtros['acceso_permitido'] = $_GET['acceso_permitido'] === '1' ? true : false;
        }
        
        // Generar informe
        $contenido = generarInformeRegistros($filtros, $formato);
        
        // Configurar cabeceras de respuesta según el formato
        if ($formato === 'pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="informe_registros_' . date('Y-m-d') . '.pdf"');
        } else {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="informe_registros_' . date('Y-m-d') . '.csv"');
        }
        
        // Enviar contenido
        echo $contenido;
        exit;
        
    default:
        // Acción desconocida, redirigir al listado
        header('Location: ' . URL_BASE . '/public/registros.php');
        exit;
}

// Incluir la plantilla de pie de página
if (!in_array($action, ['exportar'])) {
    require_once __DIR__ . '/../views/templates/footer.php';
}
?>
