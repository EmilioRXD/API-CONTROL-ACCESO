const tarjetasData = [
    {
        id: 1,
        numero: 'T-001-2023',
        cedula: '25.789.432',
        nombre: 'Carlos',
        apellido: 'Rodríguez',
        fechaEmision: '01/03/2023',
        estado: 'activa'
    },
    {
        id: 2,
        numero: 'T-002-2023',
        cedula: '26.543.218',
        nombre: 'María',
        apellido: 'González',
        fechaEmision: '01/03/2023',
        estado: 'activa'
    },
    {
        id: 3,
        numero: 'T-003-2023',
        cedula: '28.765.432',
        nombre: 'Luis',
        apellido: 'Martínez',
        fechaEmision: '15/03/2023',
        estado: 'inactiva'
    },
    {
        id: 4,
        numero: 'T-004-2023',
        cedula: '29.876.543',
        nombre: 'Ana',
        apellido: 'Hernández',
        fechaEmision: '12/04/2023',
        estado: 'pendiente'
    }
];

// Para simular una base de datos más grande
function generateMoreTarjetas(count) {
    const nombres = ['Juan', 'Pedro', 'Elena', 'Roberto', 'Gabriela', 'Miguel', 'Sofía', 'Carmen', 'José', 'Laura'];
    const apellidos = ['Martínez', 'López', 'Fernández', 'Torres', 'Ramírez', 'Sánchez', 'Hernández', 'Jiménez', 'Morales', 'Castro'];
    const estados = ['activa', 'inactiva', 'pendiente'];
    
    const generatedTarjetas = [];
    
    for (let i = 0; i < count; i++) {
        const id = tarjetasData.length + i + 1;
        const randomNombre = nombres[Math.floor(Math.random() * nombres.length)];
        const randomApellido = apellidos[Math.floor(Math.random() * apellidos.length)];
        const randomEstado = estados[Math.floor(Math.random() * estados.length)];
        
        generatedTarjetas.push({
            id: id,
            numero: `T-${(id + 4).toString().padStart(3, '0')}-2023`,
            cedula: `${Math.floor(20000000 + Math.random() * 10000000)}.${Math.floor(Math.random() * 1000000)}`,
            nombre: randomNombre,
            apellido: randomApellido,
            fechaEmision: `${Math.floor(1 + Math.random() * 28).toString().padStart(2, '0')}/${Math.floor(1 + Math.random() * 12).toString().padStart(2, '0')}/2023`,
            estado: randomEstado
        });
    }
    
    return generatedTarjetas;
}

// Generar tarjetas adicionales para demostración
const allTarjetas = [...tarjetasData, ...generateMoreTarjetas(46)]; // Total: 50 tarjetas

// Variables de estado para la paginación y filtrado
let currentPage = 1;
let itemsPerPage = 10;
let filteredTarjetas = [...allTarjetas];
let activeFilters = {
    search: '',
    estado: ''
};

// Elementos DOM
const tarjetasContainer = document.getElementById('students-container');
const searchInput = document.getElementById('student-search');
const filterButtons = document.querySelectorAll('.filter-button');
const perPageSelect = document.getElementById('per-page-select');
const prevPageBtn = document.getElementById('prev-page');
const nextPageBtn = document.getElementById('next-page');
const paginationPages = document.getElementById('pagination-pages');
const pageStartEl = document.getElementById('page-start');
const pageEndEl = document.getElementById('page-end');
const totalTarjetasEl = document.getElementById('total-students');
const tarjetasCountEl = document.getElementById('students-count');
const resetFiltersBtn = document.getElementById('reset-filters');
const advancedFiltersToggle = document.getElementById('advanced-filters-toggle');
const advancedFiltersPanel = document.getElementById('advanced-filters-panel');

// Función para renderizar tarjetas
function renderTarjetas() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const tarjetasToShow = filteredTarjetas.slice(startIndex, endIndex);
    
    // Limpiar contenedor
    tarjetasContainer.innerHTML = '';
    
    if (tarjetasToShow.length === 0) {
        tarjetasContainer.innerHTML = '<div class="no-results">No se encontraron tarjetas que coincidan con los criterios de búsqueda.</div>';
    } else {
        // Renderizar cada tarjeta
        tarjetasToShow.forEach(tarjeta => {
            const tarjetaItem = document.createElement('div');
            tarjetaItem.className = 'accordion-item';
            
            // Determinar si la tarjeta tiene pago pendiente
            if (tarjeta.estado === 'pendiente') {
                tarjetaItem.classList.add('payment-pending');
            }
            
            // Determinar el tipo de contenedor de estado según el estado de la tarjeta
            let statusContainerClass = '';
            let statusIndicatorClass = '';
            let statusTitle = '';
            let statusText = '';
            
            if (tarjeta.estado === 'activa') {
                statusContainerClass = 'status-entrada-container';
                statusIndicatorClass = 'status-entrada';
                statusTitle = 'Esta tarjeta está activa y puede ser utilizada';
                statusText = 'Activa';
            } else if (tarjeta.estado === 'inactiva') {
                statusContainerClass = 'status-denegado-container';
                statusIndicatorClass = 'status-denegado';
                statusTitle = 'Esta tarjeta está inactiva y no puede ser utilizada';
                statusText = 'Inactiva';
            } else if (tarjeta.estado === 'pendiente') {
                // Mantenemos la clase del elemento como payment-pending y usamos el estilo de denegado
                statusContainerClass = 'status-denegado-container';
                statusIndicatorClass = 'status-denegado';
                statusTitle = 'Esta tarjeta tiene pagos pendientes';
                statusText = 'Pendiente';
            }

            tarjetaItem.innerHTML = `
                <div class="accordion-header">
                    <div class="accordion-title">
                        <span class="user-name">${tarjeta.nombre} ${tarjeta.apellido}</span>
                        <span class="user-date">${tarjeta.cedula}</span>
                    </div>
                    <div class="accordion-actions">
                        <div class="status-container ${statusContainerClass}" title="${statusTitle}">
                            <span class="status-indicator ${statusIndicatorClass}"></span>
                            <span>${statusText}</span>
                        </div>
                        <button class="accordion-edit-btn" title="Editar tarjeta"><i class='bx bx-pencil'></i></button>
                        <button class="accordion-toggle" title="Ver detalles"><i class='bx bx-chevron-down'></i></button>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="user-data-grid">
                        <div class="user-data-item">
                            <div class="data-label">Número:</div>
                            <div class="data-value">${tarjeta.numero}</div>
                        </div>
                        <div class="user-data-item">
                            <div class="data-label">Estudiante:</div>
                            <div class="data-value">${tarjeta.nombre} ${tarjeta.apellido}</div>
                        </div>
                        <div class="user-data-item">
                            <div class="data-label">Estado:</div>
                            <div class="data-value">${formatEstado(tarjeta.estado)}</div>
                        </div>
                        <div class="user-data-item">
                            <div class="data-label">Fecha de Emisión:</div>
                            <div class="data-value">${tarjeta.fechaEmision}</div>
                        </div>
                    </div>
                </div>
            `;
            
            tarjetasContainer.appendChild(tarjetaItem);
        });
    }
    
    // Actualizar contadores y paginación
    updateCounters();
    updatePagination();
    setupAccordionEvents();
}

// Función para actualizar contadores y paginación
function updateCounters() {
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(startIndex + itemsPerPage - 1, filteredTarjetas.length);
    
    pageStartEl.textContent = filteredTarjetas.length > 0 ? startIndex : 0;
    pageEndEl.textContent = endIndex;
    totalTarjetasEl.textContent = filteredTarjetas.length;
    tarjetasCountEl.textContent = filteredTarjetas.length;
}

// Función para actualizar controles de paginación
function updatePagination() {
    const totalPages = Math.ceil(filteredTarjetas.length / itemsPerPage);
    
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
    renderTarjetas();
    
    // Desplazarse al inicio del contenedor
    document.querySelector('.accordion-container').scrollIntoView({ behavior: 'smooth' });
}

// Función para aplicar filtros
function applyFilters() {
    // Obtener valores de los filtros
    const searchTerm = searchInput.value.toLowerCase().trim();
    const estadoFilter = activeFilters.estado;
    
    // Actualizar filtros activos
    activeFilters = {
        search: searchTerm,
        estado: estadoFilter
    };
    
    // Aplicar filtros
    filteredTarjetas = allTarjetas.filter(tarjeta => {
        // Filtro de búsqueda
        const matchesSearch = searchTerm === '' || 
            tarjeta.nombre.toLowerCase().includes(searchTerm) || 
            tarjeta.apellido.toLowerCase().includes(searchTerm) || 
            tarjeta.cedula.toLowerCase().includes(searchTerm) ||
            tarjeta.numero.toLowerCase().includes(searchTerm);
        
        // Filtro de estado
        const matchesEstado = estadoFilter === '' || tarjeta.estado === estadoFilter;
        
        return matchesSearch && matchesEstado;
    });
    
    // Restablecer a la primera página
    currentPage = 1;
    
    // Renderizar tarjetas filtradas
    renderTarjetas();
}

// Función para formatear el estado de la tarjeta
function formatEstado(estado) {
    switch (estado) {
        case 'activa':
            return 'Activa';
        case 'inactiva':
            return 'Inactiva';
        case 'pendiente':
            return 'Pago Pendiente';
        default:
            return estado.charAt(0).toUpperCase() + estado.slice(1);
    }
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

    // Eventos de botones de edición
    document.querySelectorAll('.accordion-edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            // Aquí se implementaría la lógica para editar tarjetas
            alert('Funcionalidad de edición de tarjetas estará disponible próximamente');
        });
    });
}

// Inicializar eventos
document.addEventListener('DOMContentLoaded', () => {
    // Eventos de búsqueda
    searchInput.addEventListener('input', () => {
        // Aplicar filtros después de un breve retraso para evitar muchas actualizaciones
        clearTimeout(searchInput.timer);
        searchInput.timer = setTimeout(() => {
            applyFilters();
        }, 300);
    });
    
    // Eventos de filtro
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const filterValue = button.getAttribute('data-value');
            
            // Actualizar clases visuales
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
            
            // Actualizar filtro activo
            activeFilters.estado = filterValue;
            
            // Aplicar filtros
            applyFilters();
        });
    });
    
    // Evento de reset de filtros
    resetFiltersBtn.addEventListener('click', () => {
        // Limpiar búsqueda
        searchInput.value = '';
        
        // Desactivar todos los botones de filtro
        filterButtons.forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Resetear filtros activos
        activeFilters = {
            search: '',
            estado: ''
        };
        
        // Aplicar filtros (mostrar todo)
        filteredTarjetas = [...allTarjetas];
        currentPage = 1;
        renderTarjetas();
    });
    
    // Eventos para filtros avanzados
    advancedFiltersToggle.addEventListener('click', () => {
        advancedFiltersPanel.classList.toggle('show');
        advancedFiltersToggle.classList.toggle('active');
    });
    
    // Eventos de paginación
    perPageSelect.addEventListener('change', () => {
        itemsPerPage = parseInt(perPageSelect.value);
        currentPage = 1;
        renderTarjetas();
    });
    
    prevPageBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            goToPage(currentPage - 1);
        }
    });
    
    nextPageBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredTarjetas.length / itemsPerPage);
        if (currentPage < totalPages) {
            goToPage(currentPage + 1);
        }
    });
    
    // Inicializar
    renderTarjetas();
});
