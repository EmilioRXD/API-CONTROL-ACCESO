<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../controllers/tarjetas_controller.php';
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
        // Listar tarjetas
        $tarjetas = getTarjetas();
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista de listado de tarjetas
        require_once __DIR__ . '/../views/tarjetas/list.php';
        break;
        
    case 'create':
        // Obtener escritores de tarjetas para el formulario
        $escritores = getEscritoresTarjetas();
        
        // Inicializar tarjeta vacía o con datos preestablecidos
        $tarjeta = [];
        
        // Si hay cédula de estudiante en la URL, usarla para prellenar el formulario
        if (isset($_GET['estudiante_cedula'])) {
            $tarjeta['estudiante_cedula'] = filter_input(INPUT_GET, 'estudiante_cedula', FILTER_SANITIZE_NUMBER_INT);
        }
        
        // Establecer fechas predeterminadas
        $tarjeta['fecha_emision'] = date('Y-m-d');
        $tarjeta['fecha_expiracion'] = date('Y-m-d', strtotime('+1 year'));
        
        $accion = 'create';
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/tarjetas/form.php';
        break;
        
    case 'store':
        // Procesar datos del formulario para asignar tarjeta
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar y validar datos
            $datos = [
                'estudiante_cedula' => filter_input(INPUT_POST, 'estudiante_cedula', FILTER_SANITIZE_NUMBER_INT),
                'mac_escritor' => filter_input(INPUT_POST, 'mac_escritor', FILTER_SANITIZE_STRING),
                'fecha_emision' => filter_input(INPUT_POST, 'fecha_emision', FILTER_SANITIZE_STRING),
                'fecha_expiracion' => filter_input(INPUT_POST, 'fecha_expiracion', FILTER_SANITIZE_STRING)
            ];
            
            // Validar que todos los campos requeridos estén presentes
            if (empty($datos['estudiante_cedula']) || empty($datos['mac_escritor']) || 
                empty($datos['fecha_emision']) || empty($datos['fecha_expiracion'])) {
                
                $error_message = 'Todos los campos son obligatorios.';
                $tarjeta = $datos;
                $accion = 'create';
                $escritores = getEscritoresTarjetas();
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/tarjetas/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
            
            // Intentar asignar la tarjeta
            $result = asignarTarjeta($datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Tarjeta asignada correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/tarjetas.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al asignar la tarjeta.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $tarjeta = $datos;
                $accion = 'create';
                $escritores = getEscritoresTarjetas();
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/tarjetas/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
        } else {
            // Si no es POST, redirigir al formulario de creación
            header('Location: ' . URL_BASE . '/public/tarjetas.php?action=create');
            exit;
        }
        
    case 'edit':
        // Obtener ID de la tarjeta a editar
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de tarjeta no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/tarjetas.php');
            exit;
        }
        
        // Obtener datos de la tarjeta
        $tarjeta = getTarjeta($id);
        
        if (!$tarjeta) {
            $_SESSION['flash_message'] = 'Tarjeta no encontrada.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/tarjetas.php');
            exit;
        }
        
        $accion = 'edit';
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/tarjetas/form.php';
        
    case 'update':
        // Procesar datos del formulario para actualizar tarjeta
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener ID de la tarjeta a actualizar
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            
            if (!$id) {
                $_SESSION['flash_message'] = 'ID de tarjeta no especificado.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . URL_BASE . '/public/tarjetas.php');
                exit;
            }
            
            // Sanitizar y validar datos
            $datos = [
                'fecha_emision' => filter_input(INPUT_POST, 'fecha_emision', FILTER_SANITIZE_STRING),
                'fecha_expiracion' => filter_input(INPUT_POST, 'fecha_expiracion', FILTER_SANITIZE_STRING)
            ];
            
            // Verificar estado activo
            if (isset($_POST['activa'])) {
                $datos['activa'] = (bool)$_POST['activa'];
            }
            
            // Validar que los campos requeridos estén presentes
            if (empty($datos['fecha_emision']) || empty($datos['fecha_expiracion'])) {
                $error_message = 'Las fechas de emisión y expiración son obligatorias.';
                
                // Obtener los datos de la tarjeta nuevamente
                $tarjeta = getTarjeta($id);
                // Añadir los datos del formulario
                $tarjeta = array_merge($tarjeta, $datos);
                
                $accion = 'edit';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/tarjetas/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
            
            // Intentar actualizar la tarjeta
            $result = actualizarTarjeta($id, $datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Tarjeta actualizada correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/tarjetas.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al actualizar la tarjeta.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $tarjeta = getTarjeta($id);
                $tarjeta = array_merge($tarjeta, $datos);
                $accion = 'edit';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/tarjetas/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
        } else {
            // Si no es POST, redirigir al listado
            header('Location: ' . URL_BASE . '/public/tarjetas.php');
            exit;
        }
        
    case 'view':
        // Ver detalles de una tarjeta
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de tarjeta no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/tarjetas.php');
            exit;
        }
        
        // Obtener datos de la tarjeta
        $tarjeta = getTarjeta($id);
        
        if (!$tarjeta) {
            $_SESSION['flash_message'] = 'Tarjeta no encontrada.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/tarjetas.php');
            exit;
        }
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista detallada de la tarjeta
        require_once __DIR__ . '/../views/tarjetas/view.php';
        break;
        
    case 'delete':
        // Eliminar tarjeta
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de tarjeta no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/tarjetas.php');
            exit;
        }
        
        // Intentar eliminar la tarjeta
        $success = eliminarTarjeta($id);
        
        if ($success) {
            $_SESSION['flash_message'] = 'Tarjeta eliminada correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al eliminar la tarjeta.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar al listado
        header('Location: ' . URL_BASE . '/public/tarjetas.php');
        exit;
        
    case 'activar':
    case 'desactivar':
        // Activar o desactivar tarjeta
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de tarjeta no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/tarjetas.php');
            exit;
        }
        
        $result = $action === 'activar' ? activarTarjeta($id) : desactivarTarjeta($id);
        
        if ($result) {
            $_SESSION['flash_message'] = 'Tarjeta ' . ($action === 'activar' ? 'activada' : 'desactivada') . ' correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al ' . ($action === 'activar' ? 'activar' : 'desactivar') . ' la tarjeta.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar al listado
        header('Location: ' . URL_BASE . '/public/tarjetas.php');
        exit;
        
    default:
        // Acción desconocida, redirigir al listado
        header('Location: ' . URL_BASE . '/public/tarjetas.php');
        exit;
}

// Incluir la plantilla de pie de página
if (!in_array($action, ['store', 'update', 'delete', 'activar', 'desactivar'])) {
    require_once __DIR__ . '/../views/templates/footer.php';
}
?>
