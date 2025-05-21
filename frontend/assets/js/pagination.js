/**
 * Sistema de paginación para tablas de datos
 */
class TablePagination {
    constructor(tableId, options = {}) {
        this.tableId = tableId;
        this.table = document.getElementById(tableId);
        
        if (!this.table) {
            console.error(`Tabla con ID ${tableId} no encontrada`);
            return;
        }
        
        // Opciones por defecto
        this.options = {
            rowsPerPage: options.rowsPerPage || 10,
            pageSizes: options.pageSizes || [5, 10, 25, 50, 100],
            pageContainerId: options.pageContainerId || `${tableId}-pagination`,
            pageInfoId: options.pageInfoId || `${tableId}-info`,
            pageSelectId: options.pageSelectId || `${tableId}-select`
        };
        
        // Estado
        this.currentPage = 1;
        this.totalRows = 0;
        this.totalPages = 0;
        
        // Elementos del DOM
        this.tbody = this.table.querySelector('tbody');
        this.rows = Array.from(this.tbody.querySelectorAll('tr'));
        this.totalRows = this.rows.length;
        this.paginationContainer = document.getElementById(this.options.pageContainerId);
        this.pageInfo = document.getElementById(this.options.pageInfoId);
        this.pageSelect = document.getElementById(this.options.pageSelectId);
        
        // Inicializar
        this.init();
    }
    
    /**
     * Inicializar la paginación
     */
    init() {
        // Si no existen los contenedores de paginación, crearlos
        if (!this.paginationContainer) {
            this.createUIElements();
        }
        
        // Configurar el selector de tamaño de página
        this.setupPageSizeSelector();
        
        // Calcular el número total de páginas
        this.totalPages = Math.ceil(this.totalRows / this.options.rowsPerPage);
        
        // Renderizar la primera página
        this.renderPage(1);
        
        // Actualizar la información de paginación
        this.updatePaginationInfo();
        
        // Renderizar los controles de paginación
        this.renderPaginationControls();
    }
    
    /**
     * Crear los elementos UI necesarios para la paginación
     */
    createUIElements() {
        // Crear contenedor de paginación
        const paginationWrapper = document.createElement('div');
        paginationWrapper.className = 'pagination-wrapper mt-3';
        
        // Crear fila para los controles
        const row = document.createElement('div');
        row.className = 'row align-items-center mx-0';
        
        // Columna para selector de tamaño de página
        const colSelect = document.createElement('div');
        colSelect.className = 'col-md-3 col-sm-12 mb-2';
        
        const selectGroup = document.createElement('div');
        selectGroup.className = 'input-group input-group-sm';
        
        const selectLabel = document.createElement('span');
        selectLabel.className = 'input-group-text';
        selectLabel.textContent = 'Mostrar';
        
        this.pageSelect = document.createElement('select');
        this.pageSelect.className = 'form-select form-select-sm';
        this.pageSelect.id = this.options.pageSelectId;
        
        selectGroup.appendChild(selectLabel);
        selectGroup.appendChild(this.pageSelect);
        colSelect.appendChild(selectGroup);
        
        // Columna para información de página
        const colInfo = document.createElement('div');
        colInfo.className = 'col-md-3 col-sm-12 mb-2';
        
        this.pageInfo = document.createElement('div');
        this.pageInfo.className = 'pagination-info text-muted';
        this.pageInfo.id = this.options.pageInfoId;
        
        colInfo.appendChild(this.pageInfo);
        
        // Columna para controles de navegación
        const colNav = document.createElement('div');
        colNav.className = 'col-md-6 col-sm-12 mb-2';
        
        this.paginationContainer = document.createElement('ul');
        this.paginationContainer.className = 'pagination pagination-sm justify-content-end';
        this.paginationContainer.id = this.options.pageContainerId;
        
        colNav.appendChild(this.paginationContainer);
        
        // Añadir todo al DOM
        row.appendChild(colSelect);
        row.appendChild(colInfo);
        row.appendChild(colNav);
        paginationWrapper.appendChild(row);
        
        // Insertar después de la tabla
        this.table.parentNode.insertBefore(paginationWrapper, this.table.nextSibling);
    }
    
    /**
     * Configurar el selector de tamaño de página
     */
    setupPageSizeSelector() {
        if (!this.pageSelect) return;
        
        // Limpiar opciones existentes
        this.pageSelect.innerHTML = '';
        
        // Añadir opciones basadas en pageSizes
        this.options.pageSizes.forEach(size => {
            const option = document.createElement('option');
            option.value = size;
            option.textContent = `${size} registros`;
            
            if (size === this.options.rowsPerPage) {
                option.selected = true;
            }
            
            this.pageSelect.appendChild(option);
        });
        
        // Evento de cambio
        this.pageSelect.addEventListener('change', () => {
            this.options.rowsPerPage = parseInt(this.pageSelect.value);
            this.totalPages = Math.ceil(this.totalRows / this.options.rowsPerPage);
            this.currentPage = 1; // Resetear a la primera página
            this.renderPage(1);
            this.updatePaginationInfo();
            this.renderPaginationControls();
        });
    }
    
    /**
     * Renderizar una página específica
     */
    renderPage(pageNum) {
        if (pageNum < 1 || pageNum > this.totalPages) {
            return;
        }
        
        this.currentPage = pageNum;
        
        // Calcular índices
        const startIndex = (pageNum - 1) * this.options.rowsPerPage;
        const endIndex = Math.min(startIndex + this.options.rowsPerPage, this.totalRows);
        
        // Ocultar todas las filas
        this.rows.forEach(row => {
            row.style.display = 'none';
        });
        
        // Mostrar solo las filas de la página actual
        for (let i = startIndex; i < endIndex; i++) {
            if (this.rows[i]) {
                this.rows[i].style.display = '';
            }
        }
        
        // Actualizar información de paginación
        this.updatePaginationInfo();
        
        // Actualizar estado visual de los controles
        this.updatePaginationControls();
    }
    
    /**
     * Actualizar la información de paginación
     */
    updatePaginationInfo() {
        if (!this.pageInfo) return;
        
        const startIndex = (this.currentPage - 1) * this.options.rowsPerPage + 1;
        const endIndex = Math.min(startIndex + this.options.rowsPerPage - 1, this.totalRows);
        
        this.pageInfo.textContent = `Mostrando ${startIndex} a ${endIndex} de ${this.totalRows} registros`;
    }
    
    /**
     * Renderizar los controles de paginación
     */
    renderPaginationControls() {
        if (!this.paginationContainer) return;
        
        // Limpiar controles existentes
        this.paginationContainer.innerHTML = '';
        
        // Botón Primera página
        this.addPaginationButton('«', 1, this.currentPage === 1);
        
        // Botón Anterior
        this.addPaginationButton('‹', this.currentPage - 1, this.currentPage === 1);
        
        // Determinar rango de páginas a mostrar
        let startPage = Math.max(1, this.currentPage - 2);
        let endPage = Math.min(this.totalPages, startPage + 4);
        
        // Ajustar si estamos cerca del final
        if (endPage - startPage < 4 && this.totalPages > 5) {
            startPage = Math.max(1, endPage - 4);
        }
        
        // Botones de página
        for (let i = startPage; i <= endPage; i++) {
            this.addPaginationButton(i.toString(), i, false, i === this.currentPage);
        }
        
        // Botón Siguiente
        this.addPaginationButton('›', this.currentPage + 1, this.currentPage === this.totalPages);
        
        // Botón Última página
        this.addPaginationButton('»', this.totalPages, this.currentPage === this.totalPages);
    }
    
    /**
     * Añadir un botón de paginación
     */
    addPaginationButton(text, pageNum, disabled = false, active = false) {
        const li = document.createElement('li');
        li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
        
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = text;
        
        if (!disabled) {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                this.renderPage(pageNum);
                this.renderPaginationControls();
            });
        }
        
        li.appendChild(a);
        this.paginationContainer.appendChild(li);
    }
    
    /**
     * Actualizar el estado visual de los controles
     */
    updatePaginationControls() {
        if (!this.paginationContainer) return;
        
        const pageLinks = this.paginationContainer.querySelectorAll('.page-link');
        
        pageLinks.forEach((link, index) => {
            const li = link.parentNode;
            
            // Primer y segundo botón (Primera y Anterior)
            if (index === 0 || index === 1) {
                li.classList.toggle('disabled', this.currentPage === 1);
            }
            
            // Últimos dos botones (Siguiente y Última)
            if (index === pageLinks.length - 1 || index === pageLinks.length - 2) {
                li.classList.toggle('disabled', this.currentPage === this.totalPages);
            }
            
            // Botones de número de página
            if (index > 1 && index < pageLinks.length - 2) {
                const pageNum = parseInt(link.textContent);
                li.classList.toggle('active', pageNum === this.currentPage);
            }
        });
    }
}

// Inicializar paginación para todas las tablas con la clase 'paginated-table'
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('table.paginated-table');
    
    tables.forEach((table, index) => {
        // Asignar ID si no tiene
        if (!table.id) {
            table.id = `paginated-table-${index}`;
        }
        
        // Inicializar paginación
        new TablePagination(table.id);
    });
});
