/**
 * Utilidades para tablas del sistema
 */
document.addEventListener('DOMContentLoaded', function() {
    // Convertir todas las tablas en paginadas
    initAllTables();
    
    // Inicializar filtros de búsqueda
    initTableFilters();
});

/**
 * Inicializa todas las tablas del sistema
 */
function initAllTables() {
    // Seleccionar todas las tablas que no tienen la clase 'no-paginate'
    const tables = document.querySelectorAll('table:not(.no-paginate)');
    
    tables.forEach((table, index) => {
        // Asignar ID si no tiene
        if (!table.id) {
            table.id = `data-table-${index}`;
        }
        
        // Añadir clases de estilo
        table.classList.add('table-striped', 'table-hover');
        
        // Añadir clase para paginación
        if (!table.classList.contains('paginated-table')) {
            table.classList.add('paginated-table');
        }
        
        // Verificar si la tabla está vacía y mostrar mensaje
        const tbody = table.querySelector('tbody');
        if (tbody && tbody.querySelectorAll('tr').length === 0) {
            const colspan = table.querySelectorAll('thead th').length || 1;
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.setAttribute('colspan', colspan);
            td.className = 'text-center text-muted py-3';
            td.innerHTML = 'No hay datos disponibles';
            tr.appendChild(td);
            tbody.appendChild(tr);
        }
        
        // Inicializar paginación si no está ya inicializada
        if (typeof TablePagination !== 'undefined' && !table.getAttribute('data-pagination-initialized')) {
            new TablePagination(table.id);
            table.setAttribute('data-pagination-initialized', 'true');
        }
    });
}

/**
 * Inicializa filtros de búsqueda para tablas
 */
function initTableFilters() {
    const filterInputs = document.querySelectorAll('input[data-table-filter]');
    
    filterInputs.forEach(input => {
        const tableId = input.getAttribute('data-table-filter');
        
        input.addEventListener('keyup', function() {
            filterTable(tableId, this.value);
        });
    });
}

/**
 * Filtra una tabla por texto
 * @param {string} tableId - ID de la tabla a filtrar
 * @param {string} filterText - Texto para filtrar
 */
function filterTable(tableId, filterText) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    const filterLower = filterText.toLowerCase();
    let visibleRowCount = 0;
    
    // Filtrar filas
    rows.forEach(row => {
        // Ignorar filas de mensaje "no hay datos"
        if (row.querySelector('td[colspan]') && row.querySelectorAll('td').length === 1) {
            row.style.display = 'none';
            return;
        }
        
        let shouldShow = false;
        const cells = row.querySelectorAll('td');
        
        cells.forEach(cell => {
            if (cell.textContent.toLowerCase().includes(filterLower)) {
                shouldShow = true;
            }
        });
        
        row.style.display = shouldShow ? '' : 'none';
        if (shouldShow) visibleRowCount++;
    });
    
    // Mostrar mensaje si no hay resultados
    if (visibleRowCount === 0) {
        // Eliminar mensaje existente si lo hay
        const existingMessage = tbody.querySelector('.no-results-message');
        if (existingMessage) existingMessage.remove();
        
        const colspan = table.querySelectorAll('thead th').length || 1;
        const tr = document.createElement('tr');
        tr.className = 'no-results-message';
        const td = document.createElement('td');
        td.setAttribute('colspan', colspan);
        td.className = 'text-center text-muted py-3';
        td.innerHTML = `No se encontraron resultados para "${filterText}"`;
        tr.appendChild(td);
        tbody.appendChild(tr);
    } else {
        // Eliminar mensaje de no resultados si existe
        const noResultsRow = tbody.querySelector('.no-results-message');
        if (noResultsRow) noResultsRow.remove();
    }
    
    // Reiniciar paginación si existe
    if (typeof TablePagination !== 'undefined') {
        // Buscar la instancia de paginación
        const tableInstance = document.querySelector(`[data-table-id="${tableId}"]`);
        if (tableInstance) {
            // Reiniciar la paginación a la primera página
            const paginationInstance = tableInstance._paginationInstance;
            if (paginationInstance) {
                paginationInstance.currentPage = 1;
                paginationInstance.renderPage(1);
                paginationInstance.updatePaginationInfo();
                paginationInstance.renderPaginationControls();
            }
        } else {
            // Si no se encuentra la instancia, intentar con el evento
            const paginationContainer = document.getElementById(`${tableId}-pagination`);
            if (paginationContainer) {
                // Forzar redibujado de la paginación
                const event = new Event('pagination-reset');
                paginationContainer.dispatchEvent(event);
            }
        }
    }
}

/**
 * Función para filtrar tablas (compatibilidad con código existente)
 */
function filtrarTabla() {
    const inputFiltro = document.getElementById('filtro');
    if (!inputFiltro) return;
    
    const tableId = inputFiltro.getAttribute('data-table-filter') || 'data-table-0';
    filterTable(tableId, inputFiltro.value);
}
