/**
 * Script para añadir funcionalidad avanzada a tablas de datos
 * Incluye: ordenamiento, filtrado de columnas, selección de registros por página
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todas las tablas con clase .data-table
    initializeDataTables();
});

/**
 * Inicializa todas las tablas de datos en la página
 */
function initializeDataTables() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        // Añadir controles de registros por página
        addRecordsPerPageControl(table);
        
        // Añadir filtros para columnas
        addColumnFilters(table);
        
        // Añadir ordenamiento de columnas
        addSortingFeature(table);
        
        // Actualizar contador de resultados
        updateResultsCounter(table);
    });
}

/**
 * Añade controles para seleccionar número de registros por página
 */
function addRecordsPerPageControl(table) {
    // Crear contenedor para opciones de tabla
    const tableId = table.id || 'dataTable' + Math.floor(Math.random() * 1000);
    table.id = tableId;
    
    const tableContainer = table.parentNode;
    const optionsContainer = document.createElement('div');
    optionsContainer.className = 'table-options mb-3';
    
    // Crear selector de registros por página
    const recordsPerPageContainer = document.createElement('div');
    recordsPerPageContainer.className = 'records-per-page';
    recordsPerPageContainer.innerHTML = `
        <label for="recordsPerPage-${tableId}">Mostrar 
            <select id="recordsPerPage-${tableId}" class="form-select form-select-sm d-inline-block w-auto">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select> registros
        </label>
    `;
    
    // Crear contador de resultados
    const resultsCounterContainer = document.createElement('div');
    resultsCounterContainer.className = 'records-info';
    resultsCounterContainer.id = `counter-${tableId}`;
    resultsCounterContainer.textContent = 'Mostrando 0 registros';
    
    // Añadir elementos al contenedor
    optionsContainer.appendChild(recordsPerPageContainer);
    optionsContainer.appendChild(resultsCounterContainer);
    
    // Insertar antes de la tabla
    tableContainer.insertBefore(optionsContainer, table);
    
    // Evento para cambiar registros por página
    const recordsPerPageSelect = document.getElementById(`recordsPerPage-${tableId}`);
    recordsPerPageSelect.addEventListener('change', function() {
        const limit = parseInt(this.value);
        const currentUrl = new URL(window.location.href);
        
        currentUrl.searchParams.set('limit', limit);
        currentUrl.searchParams.set('skip', 0);  // Resetear a la primera página
        
        window.location.href = currentUrl.toString();
    });
    
    // Establecer el valor seleccionado basado en la URL actual
    const urlParams = new URLSearchParams(window.location.search);
    const currentLimit = urlParams.get('limit');
    if (currentLimit) {
        recordsPerPageSelect.value = currentLimit;
    }
}

/**
 * Añade filtros para cada columna de la tabla
 */
function addColumnFilters(table) {
    const headers = table.querySelectorAll('thead th');
    
    // Crear una nueva fila para los filtros
    const filterRow = document.createElement('tr');
    filterRow.className = 'filters';
    
    headers.forEach(header => {
        const cell = document.createElement('th');
        
        // Ignorar columnas de botones/acciones
        if (header.textContent.trim() !== 'Detalles' && 
            header.textContent.trim() !== 'Acciones') {
            
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control form-control-sm column-filter';
            input.placeholder = 'Filtrar...';
            
            // Obtener el nombre de la columna de datos
            const columnName = header.dataset.column || header.textContent.toLowerCase().replace(/\s+/g, '_');
            input.dataset.column = columnName;
            
            // Establecer valor del filtro si existe en la URL
            const urlParams = new URLSearchParams(window.location.search);
            const filterValue = urlParams.get(`filter_${columnName}`);
            if (filterValue) {
                input.value = filterValue;
                header.classList.add('filtered');
            }
            
            // Evento para aplicar filtro
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyColumnFilter(input);
                }
            });
            
            cell.appendChild(input);
        }
        
        filterRow.appendChild(cell);
    });
    
    // Añadir la fila de filtros después de los encabezados
    const thead = table.querySelector('thead');
    thead.appendChild(filterRow);
}

/**
 * Aplica un filtro de columna
 */
function applyColumnFilter(inputElement) {
    const columnName = inputElement.dataset.column;
    const filterValue = inputElement.value.trim();
    
    const currentUrl = new URL(window.location.href);
    
    if (filterValue) {
        currentUrl.searchParams.set(`filter_${columnName}`, filterValue);
    } else {
        currentUrl.searchParams.delete(`filter_${columnName}`);
    }
    
    // Resetear paginación
    currentUrl.searchParams.set('skip', 0);
    
    window.location.href = currentUrl.toString();
}

/**
 * Añade funcionalidad de ordenamiento a las columnas
 */
function addSortingFeature(table) {
    const headers = table.querySelectorAll('thead th');
    
    headers.forEach(header => {
        // Ignorar columnas de botones/acciones
        if (header.textContent.trim() !== 'Detalles' && 
            header.textContent.trim() !== 'Acciones') {
            
            header.classList.add('sortable');
            
            // Obtener el nombre de la columna de datos
            const columnName = header.dataset.column || header.textContent.toLowerCase().replace(/\s+/g, '_');
            header.dataset.column = columnName;
            
            // Verificar si esta columna está actualmente ordenada
            const urlParams = new URLSearchParams(window.location.search);
            const sortColumn = urlParams.get('sort');
            const sortOrder = urlParams.get('order');
            
            if (sortColumn === columnName) {
                header.classList.add(sortOrder === 'desc' ? 'desc' : 'asc');
            }
            
            // Evento de clic para ordenar
            header.addEventListener('click', function() {
                const currentUrl = new URL(window.location.href);
                let newOrder = 'asc';
                
                // Cambiar dirección si ya estaba ordenado por esta columna
                if (sortColumn === columnName && sortOrder === 'asc') {
                    newOrder = 'desc';
                }
                
                currentUrl.searchParams.set('sort', columnName);
                currentUrl.searchParams.set('order', newOrder);
                
                window.location.href = currentUrl.toString();
            });
        }
    });
}

/**
 * Actualiza el contador de resultados
 */
function updateResultsCounter(table) {
    const tableId = table.id;
    const counter = document.getElementById(`counter-${tableId}`);
    
    if (counter) {
        const rows = table.querySelectorAll('tbody tr');
        const total = rows.length;
        
        // Obtener información de paginación
        const urlParams = new URLSearchParams(window.location.search);
        const skip = parseInt(urlParams.get('skip')) || 0;
        const limit = parseInt(urlParams.get('limit')) || 20;
        
        counter.textContent = `Mostrando ${total} registros (${skip + 1}-${Math.min(skip + limit, skip + total)} de ${total} total)`;
    }
}

/**
 * Exportar tabla a PDF con filtros actuales
 * Esta función prepara la URL para exportar con todos los filtros
 */
function exportTableToPDF(tipo) {
    const currentUrl = new URL(window.location.href);
    
    // Crear URL de exportación
    const exportUrl = new URL(`${window.location.origin}${URL_BASE}/public/reportes.php`);
    exportUrl.searchParams.set('action', 'generar');
    exportUrl.searchParams.set('tipo_reporte', tipo);
    exportUrl.searchParams.set('formato', 'pdf');
    
    // Copiar todos los filtros de la URL actual
    currentUrl.searchParams.forEach((value, key) => {
        if (key.startsWith('filter_') || 
            key === 'fecha_inicio' || 
            key === 'fecha_fin' || 
            key === 'acceso_permitido' || 
            key === 'ubicacion_controlador') {
            exportUrl.searchParams.set(key, value);
        }
    });
    
    // Abrir en nueva ventana
    window.open(exportUrl.toString(), '_blank');
}
