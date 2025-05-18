<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
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
        // Listar estudiantes
        $estudiantes = getEstudiantes();
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista de listado de estudiantes
        require_once __DIR__ . '/../views/estudiantes/list.php';
        break;
        
    case 'create':
        // Obtener carreras para el formulario
        $carreras = getCarreras();
        
        // Inicializar estudiante vacío
        $estudiante = [];
        $accion = 'create';
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/estudiantes/form.php';
        break;
        
    case 'store':
        // Procesar datos del formulario para crear estudiante
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar y validar datos
            $datos = [
                'cedula' => filter_input(INPUT_POST, 'cedula', FILTER_SANITIZE_NUMBER_INT),
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                'apellido' => filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING),
                'id_carrera' => filter_input(INPUT_POST, 'id_carrera', FILTER_SANITIZE_NUMBER_INT)
            ];
            
            // Intentar crear el estudiante
            $result = crearEstudiante($datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Estudiante creado correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/estudiantes.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al crear el estudiante.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $estudiante = $datos;
                $accion = 'create';
                $carreras = getCarreras();
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/estudiantes/form.php';
            }
        } else {
            // Si no es POST, redirigir al formulario de creación
            header('Location: ' . URL_BASE . '/public/estudiantes.php?action=create');
            exit;
        }
        break;
        
    case 'edit':
        // Obtener cédula del estudiante a editar
        $cedula = filter_input(INPUT_GET, 'cedula', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$cedula) {
            $_SESSION['flash_message'] = 'Cédula de estudiante no especificada.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/estudiantes.php');
            exit;
        }
        
        // Obtener datos del estudiante
        $estudiante = getEstudiante($cedula);
        
        if (!$estudiante) {
            $_SESSION['flash_message'] = 'Estudiante no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/estudiantes.php');
            exit;
        }
        
        // Obtener carreras para el formulario
        $carreras = getCarreras();
        $accion = 'edit';
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/estudiantes/form.php';
        break;
        
    case 'update':
        // Procesar datos del formulario para actualizar estudiante
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener cédula del estudiante a actualizar
            $cedula = filter_input(INPUT_GET, 'cedula', FILTER_SANITIZE_NUMBER_INT);
            
            if (!$cedula) {
                $_SESSION['flash_message'] = 'Cédula de estudiante no especificada.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . URL_BASE . '/public/estudiantes.php');
                exit;
            }
            
            // Sanitizar y validar datos
            $datos = [
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                'apellido' => filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING),
                'id_carrera' => filter_input(INPUT_POST, 'id_carrera', FILTER_SANITIZE_NUMBER_INT)
            ];
            
            // Intentar actualizar el estudiante
            $result = actualizarEstudiante($cedula, $datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Estudiante actualizado correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/estudiantes.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al actualizar el estudiante.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $estudiante = array_merge(['cedula' => $cedula], $datos);
                $accion = 'edit';
                $carreras = getCarreras();
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/estudiantes/form.php';
            }
        } else {
            // Si no es POST, redirigir al listado
            header('Location: ' . URL_BASE . '/public/estudiantes.php');
            exit;
        }
        break;
        
    case 'delete':
        // Eliminar estudiante
        $cedula = filter_input(INPUT_GET, 'cedula', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$cedula) {
            $_SESSION['flash_message'] = 'Cédula de estudiante no especificada.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/estudiantes.php');
            exit;
        }
        
        // Intentar eliminar el estudiante
        $success = eliminarEstudiante($cedula);
        
        if ($success) {
            $_SESSION['flash_message'] = 'Estudiante eliminado correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al eliminar el estudiante.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar al listado
        header('Location: ' . URL_BASE . '/public/estudiantes.php');
        exit;
        
    case 'view':
        // Ver detalles de un estudiante
        $cedula = filter_input(INPUT_GET, 'cedula', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$cedula) {
            $_SESSION['flash_message'] = 'Cédula de estudiante no especificada.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/estudiantes.php');
            exit;
        }
        
        // Obtener datos del estudiante
        $estudiante = getEstudiante($cedula);
        
        if (!$estudiante) {
            $_SESSION['flash_message'] = 'Estudiante no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/estudiantes.php');
            exit;
        }
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista detallada del estudiante
        require_once __DIR__ . '/../views/estudiantes/view.php';
        break;
        
    default:
        // Acción desconocida, redirigir al listado
        header('Location: ' . URL_BASE . '/public/estudiantes.php');
        exit;
}

// Incluir la plantilla de pie de página
if ($action != 'store' && $action != 'update' && $action != 'delete') {
    require_once __DIR__ . '/../views/templates/footer.php';
}
?>
