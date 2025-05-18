// Datos de ejemplo - En producción, estos datos vendrían de una API o base de datos
const registrosData = [
    {
        id: 1,
        cedula: 'V-12345678',
        nombre: 'Carlos',
        apellido: 'Rodríguez',
        fecha: '09/05/2025',
        hora: '09:15 AM',
        estado: 'temporal',
        tipoAcceso: 'entrada'
    },
    {
        id: 2,
        cedula: 'V-87654321',
        nombre: 'María',
        apellido: 'García',
        fecha: '09/05/2025',
        hora: '08:30 AM',
        estado: 'normal',
        tipoAcceso: 'entrada'
    },
    {
        id: 3,
        cedula: 'V-23456789',
        nombre: 'Juan',
        apellido: 'Pérez',
        fecha: '09/05/2025',
        hora: '10:00 AM',
        estado: 'normal',
        tipoAcceso: 'salida'
    },
    {
        id: 4,
        cedula: 'V-98765432',
        nombre: 'Laura',
        apellido: 'Martínez',
        fecha: '09/05/2025',
        hora: '07:45 AM',
        estado: 'deuda',
        tipoAcceso: 'denegado'
    }
];

// Para simular una base de datos más grande
function generateMoreRegistros(count) {
    const nombres = ['Ana', 'Luis', 'Elena', 'Roberto', 'Gabriela', 'Miguel', 'Sofía', 'Pedro', 'Carmen', 'José'];
    const apellidos = ['Martínez', 'López', 'Fernández', 'Torres', 'Ramírez', 'Sánchez', 'Hernández', 'Jiménez', 'Morales', 'Castro'];
    const tiposAcceso = ['entrada', 'salida', 'denegado'];
    const estados = ['normal', 'temporal', 'deuda'];
    
    const generatedRegistros = [];
    
    for (let i = 0; i < count; i++) {
        const id = registrosData.length + i + 1;
        const randomNombre = nombres[Math.floor(Math.random() * nombres.length)];
        const randomApellido = apellidos[Math.floor(Math.random() * apellidos.length)];
        const randomTipoAcceso = tiposAcceso[Math.floor(Math.random() * tiposAcceso.length)];
        const randomEstado = estados[Math.floor(Math.random() * estados.length)];
        
        // Generar hora aleatoria
        const hora = `${Math.floor(Math.random() * 4) + 7}:${Math.floor(Math.random() * 60).toString().padStart(2, '0')} ${Math.random() > 0.5 ? 'AM' : 'PM'}`;
        
        // Si el acceso es denegado, el estado debe ser 'deuda'
        const estadoFinal = randomTipoAcceso === 'denegado' ? 'deuda' : randomEstado;
        
        generatedRegistros.push({
            id: id,
            cedula: `V-${Math.floor(10000000 + Math.random() * 90000000)}`,
            nombre: randomNombre,
            apellido: randomApellido,
            fecha: '09/05/2025',
            hora: hora,
            estado: estadoFinal,
            tipoAcceso: randomTipoAcceso
        });
    }
    
    return generatedRegistros;
}

// Generar registros adicionales para demostración
const allRegistros = [...registrosData, ...generateMoreRegistros(46)]; // Total: 50 registros

// Variables de estado para la paginación y filtrado
let currentPage = 1;
let itemsPerPage = 10;
let filteredRegistros = [...allRegistros];
let activeFilters = {
    search: '',
    tipoAcceso: ''
};

// Elementos DOM
const registrosContainer = document.getElementById('students-container');
const searchInput = document.getElementById('student-search');
const filterButtons = document.querySelectorAll('.filter-button');
const perPageSelect = document.getElementById('per-page-select');
const prevPageBtn = document.getElementById('prev-page');
const nextPageBtn = document.getElementById('next-page');
const paginationPages = document.getElementById('pagination-pages');
const pageStartEl = document.getElementById('page-start');
const pageEndEl = document.getElementById('page-end');
const totalRegistrosEl = document.getElementById('total-users');
const registrosCountEl = document.getElementById('students-count');
const resetFiltersBtn = document.getElementById('reset-filters');
const advancedFiltersToggle = document.getElementById('advanced-filters-toggle');
const advancedFiltersPanel = document.getElementById('advanced-filters-panel');

// Función para renderizar registros
function renderRegistros() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const registrosToShow = filteredRegistros.slice(startIndex, endIndex);
    
    // Limpiar contenedor
    registrosContainer.innerHTML = '';
    
    if (registrosToShow.length === 0) {
        registrosContainer.innerHTML = '<div class="no-results">No se encontraron registros que coincidan con los criterios de búsqueda.</div>';
    } else {
        // Renderizar cada registro
        registrosToShow.forEach(registro => {
            const registroItem = document.createElement('div');
            registroItem.className = 'accordion-item';
            
            // Determinar si el registro tiene acceso temporal o estado de deuda
            if (registro.estado === 'temporal' || registro.estado === 'deuda') {
                registroItem.classList.add('payment-pending');
            }
            
            // Determinar clase adicional para el tipo de acceso
            const tipoAccesoClass = registro.tipoAcceso;
            const estadoPagoClass = (registro.estado === 'temporal' || registro.estado === 'deuda') ? 'payment-pending' : '';
            
            // Determinar el tipo de contenedor de estado según el tipo de acceso
            let statusContainerClass = '';
            let statusIndicatorClass = '';
            let statusTitle = '';
            let statusText = '';
            
            if (registro.tipoAcceso === 'entrada') {
                statusContainerClass = 'status-entrada-container';
                statusIndicatorClass = 'status-entrada';
                statusTitle = 'Registro de entrada al instituto';
                statusText = 'Entrada';
            } else if (registro.tipoAcceso === 'salida') {
                statusContainerClass = 'status-salida-container';
                statusIndicatorClass = 'status-salida';
                statusTitle = 'Registro de salida del instituto';
                statusText = 'Salida';
            } else if (registro.tipoAcceso === 'denegado') {
                statusContainerClass = 'status-denegado-container';
                statusIndicatorClass = 'status-denegado';
                statusTitle = 'Acceso denegado por falta de pago';
                statusText = 'Denegado';
                
                // Si tiene deuda, mantenemos la clase payment-pending para el estilo de color naranja
                if (registro.estado === 'temporal' || registro.estado === 'deuda') {
                    registroItem.classList.add('payment-pending');
                }
            }

            registroItem.innerHTML = `
                <div class="accordion-header">
                    <div class="accordion-title">
                        <span class="user-name">${registro.nombre} ${registro.apellido}</span>
                        <span class="user-date">${formatTipoAcceso(registro.tipoAcceso)}: ${registro.hora}</span>
                    </div>
                    <div class="accordion-actions">
                        <div class="status-container ${statusContainerClass}" title="${statusTitle}">
                            <span class="status-indicator ${statusIndicatorClass}"></span>
                            <span>${statusText}</span>
                        </div>
                        <button class="accordion-toggle"><i class='bx bx-chevron-down'></i></button>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="user-data-grid">
                        <div class="user-data-item">
                            <div class="data-label">Cédula:</div>
                            <div class="data-value">${registro.cedula}</div>
                        </div>
                        <div class="user-data-item">
                            <div class="data-label">Fecha:</div>
                            <div class="data-value">${registro.fecha}</div>
                        </div>
                        <div class="user-data-item">
                            <div class="data-label">Registro:</div>
                            <div class="data-value ${tipoAccesoClass}">${formatTipoAcceso(registro.tipoAcceso)}</div>
                        </div>
                        <div class="user-data-item">
                            <div class="data-label">Estado:</div>
                            <div class="data-value ${estadoPagoClass}">${formatEstado(registro.estado)}</div>
                        </div>
                    </div>
                </div>
            `;
            
            registrosContainer.appendChild(registroItem);
        });
    }
    
    // Actualizar contadores y paginación
    updateCounters();
    updatePagination();
    setupAccordionEvents();
}

// Formatear tipo de acceso para mostrar
function formatTipoAcceso(tipo) {
    const formatos = {
        'entrada': 'Entrada',
        'salida': 'Salida',
        'denegado': 'Denegado'
    };
    
    return formatos[tipo] || tipo;
}

// Formatear estado para mostrar
function formatEstado(estado) {
    const formatos = {
        'normal': 'Normal',
        'temporal': 'Acceso Temporal',
        'deuda': 'Pago Pendiente'
    };
    
    return formatos[estado] || estado;
}

// Función para actualizar contadores y paginación
function updateCounters() {
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(startIndex + itemsPerPage - 1, filteredRegistros.length);
    
    if (pageStartEl && pageEndEl) {
        pageStartEl.textContent = filteredRegistros.length > 0 ? startIndex : 0;
        pageEndEl.textContent = endIndex;
    }
    
    if (totalRegistrosEl) {
        totalRegistrosEl.textContent = filteredRegistros.length;
    }
    
    if (registrosCountEl) {
        registrosCountEl.textContent = filteredRegistros.length;
    }
}

// Función para actualizar controles de paginación
function updatePagination() {
    const totalPages = Math.ceil(filteredRegistros.length / itemsPerPage);
    
    // Actualizar estado de botones de navegación
    prevPageBtn.disabled = currentPage <= 1;
    nextPageBtn.disabled = currentPage >= totalPages;
    
    // Generar botones de página
    paginationPages.innerHTML = '';
    
    if (totalPages <= 5) {
        // Mostrar todas las páginas si son 5 o menos
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `page-number ${i === currentPage ? 'active' : ''}`;
            pageBtn.textContent = i;
            pageBtn.addEventListener('click', () => goToPage(i));
            paginationPages.appendChild(pageBtn);
        }
    } else {
        // Lógica para mostrar páginas con elipsis
        let pagesToShow = [1];
        
        if (currentPage > 3) {
            pagesToShow.push('...');
        }
        
        // Páginas alrededor de la página actual
        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
            pagesToShow.push(i);
        }
        
        if (currentPage < totalPages - 2) {
            pagesToShow.push('...');
        }
        
        if (totalPages > 1) {
            pagesToShow.push(totalPages);
        }
        
        // Renderizar botones de página
        pagesToShow.forEach(page => {
            if (page === '...') {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'page-ellipsis';
                ellipsis.textContent = '...';
                paginationPages.appendChild(ellipsis);
            } else {
                const pageBtn = document.createElement('button');
                pageBtn.className = `page-number ${page === currentPage ? 'active' : ''}`;
                pageBtn.textContent = page;
                pageBtn.addEventListener('click', () => goToPage(page));
                paginationPages.appendChild(pageBtn);
            }
        });
    }
}

// Función para ir a una página específica
function goToPage(page) {
    currentPage = page;
    renderRegistros();
    
    // Desplazarse al principio de la lista
    document.querySelector('.accordion-container').scrollIntoView({ behavior: 'smooth' });
}

// Función para aplicar filtros
function applyFilters() {
    // Filtrar por búsqueda y tipo de acceso
    filteredRegistros = allRegistros.filter(registro => {
        const searchMatch = !activeFilters.search || 
            `${registro.nombre} ${registro.apellido}`.toLowerCase().includes(activeFilters.search.toLowerCase()) ||
            registro.cedula.toLowerCase().includes(activeFilters.search.toLowerCase());
        
        // Filtrar por tipo de acceso
        const tipoAccesoMatch = !activeFilters.tipoAcceso || 
            registro.tipoAcceso === activeFilters.tipoAcceso;
        
        return searchMatch && tipoAccesoMatch;
    });
    
    // Resetear a primera página
    currentPage = 1;
    renderRegistros();
}

// Función para configurar eventos de acordeón
function setupAccordionEvents() {
    document.querySelectorAll('.accordion-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const accordionItem = button.closest('.accordion-item');

            // Cerrar otros items
            document.querySelectorAll('.accordion-item').forEach(item => {
                if (item !== accordionItem) {
                    item.classList.remove('active');
                    const otherContent = item.querySelector('.accordion-content');
                    otherContent.style.maxHeight = null;
                    item.querySelector('.accordion-toggle').innerHTML = '<i class="bx bx-chevron-down"></i>';
                }
            });

            // Abrir/cerrar el item actual
            accordionItem.classList.toggle('active');
            const content = accordionItem.querySelector('.accordion-content');

            if (accordionItem.classList.contains('active')) {
                content.style.maxHeight = content.scrollHeight + "px";
                button.innerHTML = '<i class="bx bx-chevron-up"></i>';
            } else {
                content.style.maxHeight = null;
                button.innerHTML = '<i class="bx bx-chevron-down"></i>';
            }
        });
    });
}

// Inicializar eventos
document.addEventListener('DOMContentLoaded', () => {
    // Eventos de búsqueda
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            // Aplicar filtros después de un breve retraso para evitar muchas actualizaciones
            clearTimeout(searchInput.timer);
            searchInput.timer = setTimeout(() => {
                activeFilters.search = searchInput.value.trim().toLowerCase();
                applyFilters();
            }, 300);
        });
    }
    
    // Eventos de filtros de tipo de acceso
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Toggle clase activa
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Establecer filtro activo
            activeFilters.tipoAcceso = button.dataset.value;
            applyFilters();
        });
    });
    
    // Toggle panel de filtros avanzados
    if (advancedFiltersToggle && advancedFiltersPanel) {
        advancedFiltersToggle.addEventListener('click', () => {
            advancedFiltersPanel.classList.toggle('active');
        });
    }
    
    // Restablecer filtros
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', () => {
            // Limpiar todos los filtros activos
            activeFilters = { search: '', tipoAcceso: '' };
            
            // Limpiar campo de búsqueda
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Quitar clases activas de botones de filtro
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Actualizar vista
            applyFilters();
        });
    }
    
    // Eventos de paginación
    if (perPageSelect) {
        perPageSelect.addEventListener('change', () => {
            itemsPerPage = parseInt(perPageSelect.value);
            currentPage = 1;
            renderRegistros();
        });
    }
    
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                goToPage(currentPage - 1);
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredRegistros.length / itemsPerPage);
            if (currentPage < totalPages) {
                goToPage(currentPage + 1);
            }
        });
    }
    
    // Inicializar
    renderRegistros();
});
