<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../controllers/pagos_controller.php';
require_once __DIR__ . '/../controllers/estudiantes_controller.php';

// Verificar autenticación
requireAuth();

// Mostrar la barra de navegación
$showNavbar = true;

// Obtener la acción del query string
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Procesar la acción
switch ($action) {
    case 'index':
        // Obtener filtro de estado si existe
        $estado = isset($_GET['estado']) ? $_GET['estado'] : null;
        
        // Listar pagos
        $pagos = getPagos(0, 100, $estado);
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista de listado de pagos
        require_once __DIR__ . '/../views/pagos/list.php';
        break;
        
    case 'create':
        // Obtener cuotas para el formulario
        $cuotas = getCuotas();
        
        // Inicializar pago vacío o con datos preestablecidos
        $pago = [
            'estado' => 'PENDIENTE'
        ];
        
        // Si hay cédula de estudiante en la URL, usarla para prellenar el formulario
        if (isset($_GET['estudiante_cedula'])) {
            $pago['estudiante_cedula'] = filter_input(INPUT_GET, 'estudiante_cedula', FILTER_SANITIZE_NUMBER_INT);
        }
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/pagos/form.php';
        break;
        
    case 'store':
        // Procesar datos del formulario para crear pago
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar y validar datos
            $datos = [
                'estudiante_cedula' => filter_input(INPUT_POST, 'estudiante_cedula', FILTER_SANITIZE_NUMBER_INT),
                'cuota_id' => filter_input(INPUT_POST, 'cuota_id', FILTER_SANITIZE_NUMBER_INT),
                'estado' => filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING)
            ];
            
            // Validar que todos los campos requeridos estén presentes
            if (empty($datos['estudiante_cedula']) || empty($datos['cuota_id']) || empty($datos['estado'])) {
                $error_message = 'Todos los campos son obligatorios.';
                $pago = $datos;
                $cuotas = getCuotas();
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/pagos/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
            
            // Intentar crear el pago
            $result = crearPago($datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Pago registrado correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/pagos.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al registrar el pago.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $pago = $datos;
                $cuotas = getCuotas();
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/pagos/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
        } else {
            // Si no es POST, redirigir al formulario de creación
            header('Location: ' . URL_BASE . '/public/pagos.php?action=create');
            exit;
        }
        
    case 'view':
        // Ver detalles de un pago
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de pago no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/pagos.php');
            exit;
        }
        
        // Obtener datos del pago
        $pago = getPago($id);
        
        if (!$pago) {
            $_SESSION['flash_message'] = 'Pago no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/pagos.php');
            exit;
        }
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista detallada del pago
        require_once __DIR__ . '/../views/pagos/view.php';
        break;
        
    case 'marcar_pagado':
        // Marcar un pago como pagado
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de pago no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/pagos.php');
            exit;
        }
        
        // Intentar marcar el pago como pagado
        $result = marcarPagado($id);
        
        if ($result) {
            $_SESSION['flash_message'] = 'Pago marcado como pagado correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al marcar el pago como pagado.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar según el origen
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : URL_BASE . '/public/pagos.php';
        header('Location: ' . $redirect);
        exit;
        
    case 'marcar_vencido':
        // Marcar un pago como vencido
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de pago no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/pagos.php');
            exit;
        }
        
        // Intentar marcar el pago como vencido
        $result = marcarVencido($id);
        
        if ($result) {
            $_SESSION['flash_message'] = 'Pago marcado como vencido correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al marcar el pago como vencido.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar según el origen
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : URL_BASE . '/public/pagos.php';
        header('Location: ' . $redirect);
        exit;
        
    default:
        // Acción desconocida, redirigir al listado
        header('Location: ' . URL_BASE . '/public/pagos.php');
        exit;
}

// Incluir la plantilla de pie de página
if (!in_array($action, ['store', 'marcar_pagado', 'marcar_vencido'])) {
    require_once __DIR__ . '/../views/templates/footer.php';
}
?>
