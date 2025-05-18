/**
 * pagos.js
 * Script para la gestión de la página de pagos del sistema de control de acceso
 */

document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const searchInput = document.getElementById('payment-search');
    const advancedFiltersToggle = document.getElementById('advanced-filters-toggle');
    const advancedFiltersPanel = document.querySelector('.advanced-filters');
    const filterButtons = document.querySelectorAll('.filter-button');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const accordionItems = document.querySelectorAll('.accordion-item');
    const accordionToggles = document.querySelectorAll('.accordion-title');
    const paymentsCount = document.getElementById('payments-count');
    
    // Referencias a los elementos de paginación
    const firstPageBtn = document.getElementById('first-page');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const lastPageBtn = document.getElementById('last-page');
    const paginationPages = document.getElementById('pagination-pages');
    const pageStartSpan = document.getElementById('page-start');
    const pageEndSpan = document.getElementById('page-end');
    const totalItemsSpan = document.getElementById('total-items');
    const itemsPerPageSelect = document.getElementById('items-per-page-select');
    
    // Inicializar el filtrado y la paginación
    let currentPage = 1;
    let itemsPerPage = parseInt(itemsPerPageSelect.value, 10) || 10;
    let filteredItems = [...accordionItems];
    
    // Configurar contador de resultados y paginación inicial
    paymentsCount.textContent = accordionItems.length;
    totalItemsSpan.textContent = accordionItems.length;
    
    // Inicializar paginación
    updatePagination();
    renderCurrentPage();
    
    // Función para renderizar la página actual
    function renderCurrentPage() {
        // Calcular índices de inicio y fin para los elementos de la página actual
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredItems.length);
        
        // Ocultar todos los elementos primero
        accordionItems.forEach(item => {
            item.style.display = 'none';
        });
        
        // Mostrar solo los elementos de la página actual
        for (let i = startIndex; i < endIndex; i++) {
            if (filteredItems[i]) {
                filteredItems[i].style.display = '';
            }
        }
        
        // Actualizar información de paginación
        if (filteredItems.length > 0) {
            pageStartSpan.textContent = startIndex + 1;
            pageEndSpan.textContent = endIndex;
        } else {
            pageStartSpan.textContent = '0';
            pageEndSpan.textContent = '0';
        }
        totalItemsSpan.textContent = filteredItems.length;
    }
    
    // Función para filtrar elementos según los filtros activos
    function filterItems() {
        const searchTerm = searchInput.value.trim().toLowerCase();
        const activeFilters = Array.from(document.querySelectorAll('.filter-button.active')).map(btn => btn.dataset.value);
        
        // Aplicar filtros a todos los elementos
        filteredItems = [...accordionItems].filter(item => {
            const title = item.querySelector('.accordion-title').textContent.toLowerCase();
            const content = item.querySelector('.accordion-content').textContent.toLowerCase();
            const textMatch = title.includes(searchTerm) || content.includes(searchTerm) || searchTerm === '';
            
            // Si no hay filtros activos o el texto coincide
            if (activeFilters.length === 0) {
                return textMatch;
            }
            
            // Verificar coincidencia de filtros activos
            let statusMatch = false;
            
            if (activeFilters.includes('pagado') && item.querySelector('.status-pagado')) {
                statusMatch = true;
            }
            
            if (activeFilters.includes('pendiente') && item.querySelector('.status-pendiente')) {
                statusMatch = true;
            }
            
            if (activeFilters.includes('confirmacion') && item.querySelector('.status-confirmacion')) {
                statusMatch = true;
            }
            
            return textMatch && statusMatch;
        });
        
        // Actualizar contador
        paymentsCount.textContent = filteredItems.length;
        totalItemsSpan.textContent = filteredItems.length;
        
        // Volver a la primera página y actualizar la paginación
        currentPage = 1;
        updatePagination();
        renderCurrentPage();
    }
    
    // Función para actualizar los controles de paginación
    function updatePagination() {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        
        // Habilitar/deshabilitar botones de paginación
        firstPageBtn.disabled = (currentPage === 1);
        prevPageBtn.disabled = (currentPage === 1);
        nextPageBtn.disabled = (currentPage === totalPages || totalPages === 0);
        lastPageBtn.disabled = (currentPage === totalPages || totalPages === 0);
        
        // Generar números de página
        paginationPages.innerHTML = '';
        
        if (totalPages > 0) {
            // Lógica para mostrar números de página con elipsis si hay muchas páginas
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            // Ajustar si estamos cerca del final
            if (endPage - startPage + 1 < maxVisiblePages && startPage > 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            // Añadir primera página con elipsis si es necesario
            if (startPage > 1) {
                const pageBtn = document.createElement('button');
                pageBtn.className = 'page-number' + (currentPage === 1 ? ' active' : '');
                pageBtn.textContent = '1';
                pageBtn.addEventListener('click', () => goToPage(1));
                paginationPages.appendChild(pageBtn);
                
                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'page-ellipsis';
                    ellipsis.textContent = '...';
                    paginationPages.appendChild(ellipsis);
                }
            }
            
            // Añadir páginas intermedias
            for (let i = startPage; i <= endPage; i++) {
                if (i !== 1 && i !== totalPages) { // Evitar duplicar primera y última página
                    const pageBtn = document.createElement('button');
                    pageBtn.className = 'page-number' + (currentPage === i ? ' active' : '');
                    pageBtn.textContent = i;
                    pageBtn.addEventListener('click', () => goToPage(i));
                    paginationPages.appendChild(pageBtn);
                }
            }
            
            // Añadir última página con elipsis si es necesario
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'page-ellipsis';
                    ellipsis.textContent = '...';
                    paginationPages.appendChild(ellipsis);
                }
                
                const pageBtn = document.createElement('button');
                pageBtn.className = 'page-number' + (currentPage === totalPages ? ' active' : '');
                pageBtn.textContent = totalPages;
                pageBtn.addEventListener('click', () => goToPage(totalPages));
                paginationPages.appendChild(pageBtn);
            }
        } else {
            // Si no hay elementos, mostrar solo página 1
            const pageBtn = document.createElement('button');
            pageBtn.className = 'page-number active';
            pageBtn.textContent = '1';
            pageBtn.disabled = true;
            paginationPages.appendChild(pageBtn);
        }
    }
    
    // Función para navegar a una página específica
    function goToPage(page) {
        if (page >= 1 && page <= Math.ceil(filteredItems.length / itemsPerPage)) {
            currentPage = page;
            updatePagination();
            renderCurrentPage();
            // Hacer scroll hacia arriba para una mejor experiencia de usuario
            window.scrollTo({
                top: document.querySelector('.content-area').offsetTop - 20,
                behavior: 'smooth'
            });
        }
    }
    
    // Toggle de filtros avanzados
    advancedFiltersToggle.addEventListener('click', function() {
        advancedFiltersPanel.classList.toggle('show');
        this.classList.toggle('active');
    });

    // Filtros rápidos
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
            filterItems();
        });
    });

    // Restablecer filtros
    resetFiltersBtn.addEventListener('click', function() {
        filterButtons.forEach(btn => btn.classList.remove('active'));
        advancedFiltersPanel.classList.remove('show');
        advancedFiltersToggle.classList.remove('active');
        searchInput.value = '';
        
        // Restaurar todos los elementos filtrados
        filteredItems = [...accordionItems];
        
        // Actualizar contadores
        paymentsCount.textContent = filteredItems.length;
        totalItemsSpan.textContent = filteredItems.length;
        
        // Volver a la primera página
        currentPage = 1;
        
        // Actualizar paginación y renderizar
        updatePagination();
        renderCurrentPage();
    });
    
    // Eventos para botones de paginación
    firstPageBtn.addEventListener('click', () => goToPage(1));
    prevPageBtn.addEventListener('click', () => goToPage(currentPage - 1));
    nextPageBtn.addEventListener('click', () => goToPage(currentPage + 1));
    lastPageBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        goToPage(totalPages);
    });
    
    // Evento para cambiar elementos por página
    itemsPerPageSelect.addEventListener('change', function() {
        itemsPerPage = parseInt(this.value, 10);
        currentPage = 1; // Volver a la primera página
        updatePagination();
        renderCurrentPage();
    });

    // Toggle de acordeón
    accordionToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            this.parentElement.classList.toggle('active');
        });
    });

    // Búsqueda en tiempo real
    searchInput.addEventListener('input', filterItems);
});
