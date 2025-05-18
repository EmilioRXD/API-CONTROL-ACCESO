// Datos de ejemplo - En producción, estos datos vendrían de una API o base de datos
const usersData = [
    {
        id: 1,
        correo: 'juan.perez@ejemplo.com',
        contrasena: '••••••••',
        nombre: 'Juan',
        apellido: 'Pérez',
        rol: 'admin'
    },
    {
        id: 2,
        correo: 'maria.garcia@ejemplo.com',
        contrasena: '••••••••',
        nombre: 'María',
        apellido: 'García',
        rol: 'admin'
    },
    {
        id: 3,
        correo: 'luis.rodriguez@ejemplo.com',
        contrasena: '••••••••',
        nombre: 'Luis',
        apellido: 'Rodríguez',
        rol: 'admin'
    },
    {
        id: 4,
        correo: 'sofia.hernandez@ejemplo.com',
        contrasena: '••••••••',
        nombre: 'Sofía',
        apellido: 'Hernández',
        rol: 'admin'
    }
];

// Para simular una base de datos más grande
function generateMoreUsers(count) {
    const nombres = ['Ana', 'Carlos', 'Elena', 'Roberto', 'Gabriela', 'Miguel', 'Laura', 'Pedro', 'Carmen', 'José'];
    const apellidos = ['Martínez', 'López', 'Fernández', 'Torres', 'Ramírez', 'Sánchez', 'Hernández', 'Jiménez', 'Morales', 'Castro'];
    
    const generatedUsers = [];
    
    for (let i = 0; i < count; i++) {
        const id = usersData.length + i + 1;
        const randomNombre = nombres[Math.floor(Math.random() * nombres.length)];
        const randomApellido = apellidos[Math.floor(Math.random() * apellidos.length)];
        
        generatedUsers.push({
            id: id,
            correo: `${randomNombre.toLowerCase()}.${randomApellido.toLowerCase()}@ejemplo.com`,
            contrasena: '••••••••',
            nombre: randomNombre,
            apellido: randomApellido,
            rol: 'admin' // Todos son administradores
        });
    }
    
    return generatedUsers;
}

// Generar usuarios adicionales para demostración
const allUsers = [...usersData, ...generateMoreUsers(46)]; // Total: 50 usuarios

// Variables de estado para la paginación y filtrado
let currentPage = 1;
let itemsPerPage = 10;
let filteredUsers = [...allUsers];
let activeFilters = {
    search: ''
};

// Elementos DOM
const usersContainer = document.getElementById('users-container');
const searchInput = document.getElementById('user-search');
const filterButtons = document.querySelectorAll('.filter-button');
const perPageSelect = document.getElementById('per-page-select');
const prevPageBtn = document.getElementById('prev-page');
const nextPageBtn = document.getElementById('next-page');
const paginationPages = document.getElementById('pagination-pages');
const pageStartEl = document.getElementById('page-start');
const pageEndEl = document.getElementById('page-end');
const totalUsersEl = document.getElementById('total-users');
const usersCountEl = document.getElementById('users-count');

// Función para renderizar usuarios
function renderUsers() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const usersToShow = filteredUsers.slice(startIndex, endIndex);
    
    // Limpiar contenedor
    usersContainer.innerHTML = '';
    
    if (usersToShow.length === 0) {
        usersContainer.innerHTML = '<div class="no-results">No se encontraron usuarios que coincidan con los criterios de búsqueda.</div>';
    } else {
        // Renderizar cada usuario
        usersToShow.forEach(user => {
            const userItem = document.createElement('div');
            userItem.className = 'accordion-item';
            
            userItem.innerHTML = `
                <div class="accordion-header">
                    <div class="accordion-title">
                        <span class="user-name">${user.nombre} ${user.apellido}</span>
                        <span class="user-date">${user.correo}</span>
                    </div>
                    <div class="accordion-actions">
                        <button class="accordion-edit-btn"><i class='bx bx-pencil'></i></button>
                    </div>
                </div>
            `;
            
            usersContainer.appendChild(userItem);
        });
    }
    
    // Actualizar contadores y paginación
    updateCounters();
    updatePagination();
}

// Función para actualizar contadores y paginación
function updateCounters() {
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(startIndex + itemsPerPage - 1, filteredUsers.length);
    
    pageStartEl.textContent = filteredUsers.length > 0 ? startIndex : 0;
    pageEndEl.textContent = endIndex;
    totalUsersEl.textContent = filteredUsers.length;
    usersCountEl.textContent = filteredUsers.length;
}

// Función para actualizar controles de paginación
function updatePagination() {
    const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
    
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
    renderUsers();
    
    // Desplazarse al inicio del contenedor
    document.querySelector('.accordion-container').scrollIntoView({ behavior: 'smooth' });
}

// Función para aplicar filtros
function applyFilters() {
    // Obtener valores de los filtros
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    // Actualizar filtros activos
    activeFilters = {
        search: searchTerm
    };
    
    // Aplicar filtros - solo por búsqueda ya que todos son administradores
    filteredUsers = allUsers.filter(user => {
        // Filtro de búsqueda
        return searchTerm === '' || 
            user.nombre.toLowerCase().includes(searchTerm) || 
            user.apellido.toLowerCase().includes(searchTerm) || 
            user.correo.toLowerCase().includes(searchTerm);
    });
    
    // Restablecer a la primera página
    currentPage = 1;
    
    // Renderizar usuarios filtrados
    renderUsers();
}

// Función para configurar eventos de acordeón (simplificada)
function setupAccordionEvents() {
    // Esta función ha sido simplificada porque ya no hay contenido expandible
    document.querySelectorAll('.accordion-edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            // Aquí se puede implementar la lógica para editar usuarios
            console.log('Editar usuario');
            // Por ahora solo muestra un mensaje, luego se implementará
            alert('Función de edición disponible próximamente');
        });
    });
}

// Inicializar eventos
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar Modal de Creación de Usuario
    initUserModal();
    
    // Eventos de búsqueda
    searchInput.addEventListener('input', () => {
        // Aplicar filtros después de un breve retraso para evitar muchas actualizaciones
        clearTimeout(searchInput.timer);
        searchInput.timer = setTimeout(() => {
            applyFilters();
        }, 300);
    });
    
    // No hay eventos de filtro ya que solo existen usuarios administradores
    
    // Eventos de paginación
    perPageSelect.addEventListener('change', () => {
        itemsPerPage = parseInt(perPageSelect.value);
        currentPage = 1;
        renderUsers();
    });
    
    prevPageBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            goToPage(currentPage - 1);
        }
    });
    
    nextPageBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
        if (currentPage < totalPages) {
            goToPage(currentPage + 1);
        }
    });
    
    // Inicializar
    renderUsers();
    setupAccordionEvents();
});

// Función para inicializar y manejar el modal de creación de usuario
function initUserModal() {
    const modal = document.getElementById('create-user-modal');
    const addUserBtn = document.getElementById('add-user-btn');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelUserBtn = document.getElementById('cancel-user');
    const userForm = document.getElementById('create-user-form');
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    
    // Abrir modal
    addUserBtn.addEventListener('click', () => {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevenir scroll en el fondo
    });
    
    // Cerrar modal (botón X)
    closeModalBtn.addEventListener('click', () => {
        closeModal();
    });
    
    // Cerrar modal (botón cancelar)
    cancelUserBtn.addEventListener('click', () => {
        closeModal();
    });
    
    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Función para cerrar el modal
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restaurar scroll
        userForm.reset(); // Limpiar formulario
    }
    
    // Toggle para mostrar/ocultar contraseña
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            } else {
                input.type = 'password';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            }
        });
    });
    
    // Ya no se necesita el toggle de estado
    
    // Manejar envío del formulario
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validación adicional para contraseñas
        const password = document.getElementById('user-password').value;
        const confirmPassword = document.getElementById('user-confirm-password').value;
        
        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden');
            return;
        }
        
        // Recopilar datos del formulario
        const formData = {
            nombre: document.getElementById('user-name').value,
            apellido: document.getElementById('user-lastname').value,
            correo: document.getElementById('user-email').value,
            contrasena: '••••••••' // En un caso real, se enviaría la contraseña cifrada
        };
        
        // En un caso real, aquí enviaríamos los datos a un servidor
        console.log('Datos del nuevo usuario:', formData);
        
        // Para el mockup, agregar el usuario a la lista
        const newUser = {
            id: allUsers.length + 1,
            nombre: formData.nombre,
            apellido: formData.apellido,
            correo: formData.correo,
            contrasena: formData.contrasena,
            rol: 'admin' // Por defecto todos son admin en este mockup
        };
        
        allUsers.unshift(newUser); // Agregar al inicio para que aparezca primero
        filteredUsers = [...allUsers]; // Actualizar usuarios filtrados
        currentPage = 1; // Volver a la primera página
        renderUsers(); // Renderizar usuarios
        
        // Cerrar modal
        closeModal();
        
        // Mostrar mensaje de éxito (en un caso real podría ser una notificación mejor)
        alert('Usuario creado exitosamente');
    });
}
