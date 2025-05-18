/**
 * Configuración del Sistema de Control de Acceso
 * Maneja la configuración de periodo de gracia, bloqueo de acceso,
 * carreras disponibles y cuotas.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Referencias a elementos del DOM
    // Sección de Periodo de Gracia
    const gracePeriodInput = document.getElementById('grace-period');
    const saveGracePeriodBtn = document.getElementById('save-grace-period');

    // Sección de Bloqueo de Accesos
    const blockExpiredCheckbox = document.getElementById('block-expired');
    const saveBlockSettingsBtn = document.getElementById('save-block-settings');

    // Sección de Carreras
    const addCareerBtn = document.getElementById('add-career-btn');
    const careersTable = document.getElementById('careers-table');
    const careerModal = document.getElementById('career-modal');
    const careerModalClose = document.getElementById('career-modal-close');
    const cancelCareerBtn = document.getElementById('cancel-career-btn');
    const careerForm = document.getElementById('career-form');
    const careerIdInput = document.getElementById('career-id');
    const careerNameInput = document.getElementById('career-name');
    const careerModalTitle = document.getElementById('career-modal-title');

    // Sección de Cuotas
    const addFeeBtn = document.getElementById('add-fee-btn');
    const feesTable = document.getElementById('fees-table');
    const feeModal = document.getElementById('fee-modal');
    const feeModalClose = document.getElementById('fee-modal-close');
    const cancelFeeBtn = document.getElementById('cancel-fee-btn');
    const feeForm = document.getElementById('fee-form');
    const feeIdInput = document.getElementById('fee-id');
    const feeDescriptionInput = document.getElementById('fee-description');
    const feeAmountInput = document.getElementById('fee-amount');
    const feeModalTitle = document.getElementById('fee-modal-title');

    // ==========================================
    // Funciones para el Periodo de Gracia
    // ==========================================
    function loadGracePeriod() {
        // En una implementación real, aquí cargaríamos los datos desde una API
        // Por ahora, simplemente asignamos un valor por defecto
        gracePeriodInput.value = localStorage.getItem('gracePeriod') || 7;
    }

    function saveGracePeriod() {
        const days = parseInt(gracePeriodInput.value);
        if (isNaN(days) || days < 0) {
            showAlert('Por favor ingrese un número válido de días', 'error');
            return;
        }

        // En una implementación real, aquí enviaríamos los datos a una API
        // Por ahora, simplemente guardamos en localStorage
        localStorage.setItem('gracePeriod', days);
        showAlert('Periodo de gracia guardado correctamente', 'success');
    }

    // ==========================================
    // Funciones para Bloqueo de Accesos
    // ==========================================
    function loadBlockSettings() {
        // En una implementación real, aquí cargaríamos los datos desde una API
        const blockExpired = localStorage.getItem('blockExpired');
        blockExpiredCheckbox.checked = blockExpired === null ? true : blockExpired === 'true';
    }

    function saveBlockSettings() {
        // En una implementación real, aquí enviaríamos los datos a una API
        localStorage.setItem('blockExpired', blockExpiredCheckbox.checked);
        showAlert('Configuración de bloqueo guardada correctamente', 'success');
    }

    // ==========================================
    // Funciones para las Carreras
    // ==========================================
    function loadCareers() {
        // En una implementación real, aquí cargaríamos los datos desde una API
        // Por ahora, usamos datos locales
        let careers = JSON.parse(localStorage.getItem('careers'));
        if (!careers || !Array.isArray(careers)) {
            careers = [
                { id: 1, name: 'Informática' },
                { id: 2, name: 'Administración' },
                { id: 3, name: 'Contaduría' }
            ];
            localStorage.setItem('careers', JSON.stringify(careers));
        }

        renderCareersTable(careers);
    }

    function renderCareersTable(careers) {
        const tbody = careersTable.querySelector('tbody');
        tbody.innerHTML = '';

        careers.forEach(career => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${career.id}</td>
                <td>${career.name}</td>
                <td class="actions-cell">
                    <button class="btn-icon btn-edit career-edit" data-id="${career.id}">
                        <i class="bx bx-edit-alt"></i>
                    </button>
                    <button class="btn-icon btn-delete career-delete" data-id="${career.id}">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Agregar event listeners a los botones de editar y eliminar
        addEditCareerEventListeners();
        addDeleteCareerEventListeners();
    }

    function openCareerModal(isEditing = false, career = null) {
        careerModalTitle.textContent = isEditing ? 'Editar Carrera' : 'Agregar Carrera';
        
        if (isEditing && career) {
            careerIdInput.value = career.id;
            careerNameInput.value = career.name;
        } else {
            careerIdInput.value = '';
            careerNameInput.value = '';
        }
        
        careerModal.classList.add('show');
    }

    function closeCareerModal() {
        careerModal.classList.remove('show');
    }

    function addCareer(event) {
        event.preventDefault();
        
        const careerName = careerNameInput.value.trim();
        if (!careerName) {
            showAlert('Por favor ingrese un nombre para la carrera', 'error');
            return;
        }

        // Obtener carreras existentes
        let careers = JSON.parse(localStorage.getItem('careers')) || [];
        
        // Si estamos editando
        if (careerIdInput.value) {
            const careerIndex = careers.findIndex(c => c.id == careerIdInput.value);
            if (careerIndex !== -1) {
                careers[careerIndex].name = careerName;
                showAlert('Carrera actualizada correctamente', 'success');
            }
        } else {
            // Generar ID único
            const newId = careers.length > 0 ? Math.max(...careers.map(c => c.id)) + 1 : 1;
            careers.push({
                id: newId,
                name: careerName
            });
            showAlert('Carrera agregada correctamente', 'success');
        }

        // Guardar y actualizar vista
        localStorage.setItem('careers', JSON.stringify(careers));
        loadCareers();
        closeCareerModal();
    }

    function editCareer(id) {
        const careers = JSON.parse(localStorage.getItem('careers')) || [];
        const career = careers.find(c => c.id == id);
        
        if (career) {
            openCareerModal(true, career);
        }
    }

    function deleteCareer(id) {
        if (confirm('¿Está seguro que desea eliminar esta carrera?')) {
            let careers = JSON.parse(localStorage.getItem('careers')) || [];
            careers = careers.filter(c => c.id != id);
            
            localStorage.setItem('careers', JSON.stringify(careers));
            loadCareers();
            showAlert('Carrera eliminada correctamente', 'success');
        }
    }

    function addEditCareerEventListeners() {
        document.querySelectorAll('.career-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                editCareer(id);
            });
        });
    }

    function addDeleteCareerEventListeners() {
        document.querySelectorAll('.career-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                deleteCareer(id);
            });
        });
    }

    // ==========================================
    // Funciones para las Cuotas
    // ==========================================
    function loadFees() {
        // En una implementación real, aquí cargaríamos los datos desde una API
        let fees = JSON.parse(localStorage.getItem('fees'));
        if (!fees || !Array.isArray(fees)) {
            fees = [
                { id: 1, description: 'Matrícula', amount: 50.00 },
                { id: 2, description: 'Mensualidad', amount: 30.00 },
                { id: 3, description: 'Carnet', amount: 10.00 }
            ];
            localStorage.setItem('fees', JSON.stringify(fees));
        }

        renderFeesTable(fees);
    }

    function renderFeesTable(fees) {
        const tbody = feesTable.querySelector('tbody');
        tbody.innerHTML = '';

        fees.forEach(fee => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${fee.id}</td>
                <td>${fee.description}</td>
                <td>$${fee.amount.toFixed(2)}</td>
                <td class="actions-cell">
                    <button class="btn-icon btn-edit fee-edit" data-id="${fee.id}">
                        <i class="bx bx-edit-alt"></i>
                    </button>
                    <button class="btn-icon btn-delete fee-delete" data-id="${fee.id}">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Agregar event listeners a los botones de editar y eliminar
        addEditFeeEventListeners();
        addDeleteFeeEventListeners();
    }

    function openFeeModal(isEditing = false, fee = null) {
        feeModalTitle.textContent = isEditing ? 'Editar Cuota' : 'Agregar Cuota';
        
        if (isEditing && fee) {
            feeIdInput.value = fee.id;
            feeDescriptionInput.value = fee.description;
            feeAmountInput.value = fee.amount;
        } else {
            feeIdInput.value = '';
            feeDescriptionInput.value = '';
            feeAmountInput.value = '';
        }
        
        feeModal.classList.add('show');
    }

    function closeFeeModal() {
        feeModal.classList.remove('show');
    }

    function addFee(event) {
        event.preventDefault();
        
        const description = feeDescriptionInput.value.trim();
        const amount = parseFloat(feeAmountInput.value);
        
        if (!description) {
            showAlert('Por favor ingrese una descripción para la cuota', 'error');
            return;
        }

        if (isNaN(amount) || amount < 0) {
            showAlert('Por favor ingrese un monto válido', 'error');
            return;
        }

        // Obtener cuotas existentes
        let fees = JSON.parse(localStorage.getItem('fees')) || [];
        
        // Si estamos editando
        if (feeIdInput.value) {
            const feeIndex = fees.findIndex(f => f.id == feeIdInput.value);
            if (feeIndex !== -1) {
                fees[feeIndex].description = description;
                fees[feeIndex].amount = amount;
                showAlert('Cuota actualizada correctamente', 'success');
            }
        } else {
            // Generar ID único
            const newId = fees.length > 0 ? Math.max(...fees.map(f => f.id)) + 1 : 1;
            fees.push({
                id: newId,
                description: description,
                amount: amount
            });
            showAlert('Cuota agregada correctamente', 'success');
        }

        // Guardar y actualizar vista
        localStorage.setItem('fees', JSON.stringify(fees));
        loadFees();
        closeFeeModal();
    }

    function editFee(id) {
        const fees = JSON.parse(localStorage.getItem('fees')) || [];
        const fee = fees.find(f => f.id == id);
        
        if (fee) {
            openFeeModal(true, fee);
        }
    }

    function deleteFee(id) {
        if (confirm('¿Está seguro que desea eliminar esta cuota?')) {
            let fees = JSON.parse(localStorage.getItem('fees')) || [];
            fees = fees.filter(f => f.id != id);
            
            localStorage.setItem('fees', JSON.stringify(fees));
            loadFees();
            showAlert('Cuota eliminada correctamente', 'success');
        }
    }

    function addEditFeeEventListeners() {
        document.querySelectorAll('.fee-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                editFee(id);
            });
        });
    }

    function addDeleteFeeEventListeners() {
        document.querySelectorAll('.fee-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                deleteFee(id);
            });
        });
    }

    // ==========================================
    // Función para mostrar alertas
    // ==========================================
    function showAlert(message, type = 'info') {
        // Verificar si ya existe un contenedor de alertas
        let alertContainer = document.querySelector('.alert-container');
        
        if (!alertContainer) {
            alertContainer = document.createElement('div');
            alertContainer.className = 'alert-container';
            document.body.appendChild(alertContainer);
        }

        // Crear el elemento de alerta
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <div class="alert-content">
                <i class="bx ${type === 'success' ? 'bx-check-circle' : type === 'error' ? 'bx-error-circle' : 'bx-info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="alert-close">
                <i class="bx bx-x"></i>
            </button>
        `;

        // Agregar la alerta al contenedor
        alertContainer.appendChild(alert);

        // Agregar evento para cerrar la alerta
        const closeBtn = alert.querySelector('.alert-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.remove();
            });
        }

        // Eliminar la alerta automáticamente después de 5 segundos
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    // ==========================================
    // Eventos
    // ==========================================
    // Periodo de Gracia
    saveGracePeriodBtn.addEventListener('click', saveGracePeriod);

    // Bloqueo de Accesos
    saveBlockSettingsBtn.addEventListener('click', saveBlockSettings);

    // Carreras
    addCareerBtn.addEventListener('click', () => openCareerModal());
    careerModalClose.addEventListener('click', closeCareerModal);
    cancelCareerBtn.addEventListener('click', closeCareerModal);
    careerForm.addEventListener('submit', addCareer);

    // Cuotas
    addFeeBtn.addEventListener('click', () => openFeeModal());
    feeModalClose.addEventListener('click', closeFeeModal);
    cancelFeeBtn.addEventListener('click', closeFeeModal);
    feeForm.addEventListener('submit', addFee);

    // ==========================================
    // Inicialización
    // ==========================================
    loadGracePeriod();
    loadBlockSettings();
    loadCareers();
    loadFees();
});
