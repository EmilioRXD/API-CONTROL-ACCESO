/**
 * estudiante-detalle.js
 * Script para la página de detalle de estudiante
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const editStudentBtn = document.getElementById('edit-student-btn');
    
    // Obtener el ID de estudiante de la URL (ej. ?id=123)
    const urlParams = new URLSearchParams(window.location.search);
    const studentId = urlParams.get('id');
    
    // Función para cargar los datos del estudiante
    function loadStudentData(id) {
        // Aquí se implementaría la llamada a la API para obtener los datos
        // Por ahora, usaremos datos de ejemplo
        const studentData = {
            id: id || "12345678",
            firstName: "Carlos",
            lastName: "Rodríguez",
            fullName: "Carlos Rodríguez",
            idNumber: "V-12.345.678",
            career: "Informática",
            semester: "4to Semestre",
            email: "carlos.rodriguez@ejemplo.com",
            hasPaymentPending: true,
            pendingPayments: [
                {
                    name: "Matrícula Semestre",
                    dueDate: "15/06/2025",
                    amount: "$120.00"
                },
                {
                    name: "Laboratorio de Informática",
                    dueDate: "30/06/2025",
                    amount: "$45.00"
                }
            ],
            accessHistory: [
                {
                    date: "14/05/2025",
                    time: "08:30",
                    type: "entry"
                },
                {
                    date: "14/05/2025",
                    time: "13:45",
                    type: "exit"
                },
                {
                    date: "13/05/2025",
                    time: "09:15",
                    type: "entry"
                },
                {
                    date: "13/05/2025",
                    time: "14:30",
                    type: "exit"
                }
            ]
        };
        
        // Renderizar datos en la interfaz
        renderStudentData(studentData);
    }
    
    // Función para renderizar los datos del estudiante en la interfaz
    function renderStudentData(student) {
        // Información básica
        document.getElementById('student-full-name').textContent = student.fullName;
        document.getElementById('student-id-number').textContent = student.idNumber;
        document.getElementById('student-career').textContent = student.career;
        
        // Información detallada
        document.getElementById('student-first-name').textContent = student.firstName;
        document.getElementById('student-last-name').textContent = student.lastName;
        document.getElementById('student-id-detail').textContent = student.idNumber;
        document.getElementById('student-career-detail').textContent = student.career;
        document.getElementById('student-semester').textContent = student.semester;
        document.getElementById('student-email').textContent = student.email;
        
        // Manejo del estado de pago
        const paymentStatusBadge = document.getElementById('payment-status');
        if (!student.hasPaymentPending) {
            paymentStatusBadge.style.display = 'none';
        } else {
            paymentStatusBadge.style.backgroundColor = 'var(--color-pending)';
        }
        
        // Renderizar pagos pendientes
        renderPendingPayments(student.pendingPayments);
        
        // Renderizar historial de accesos
        renderAccessHistory(student.accessHistory);
    }
    
    // Función para renderizar pagos pendientes
    function renderPendingPayments(payments) {
        const paymentsContainer = document.getElementById('pending-payments');
        
        // Limpiar el contenedor (excepto el total)
        const totalElement = paymentsContainer.querySelector('.payment-total');
        paymentsContainer.innerHTML = '';
        
        // Añadir cada pago
        let totalAmount = 0;
        
        payments.forEach(payment => {
            const paymentEl = document.createElement('div');
            paymentEl.className = 'payment-item';
            
            // Extraer el monto como número
            const amountNum = parseFloat(payment.amount.replace(/[^\d.-]/g, ''));
            totalAmount += amountNum;
            
            paymentEl.innerHTML = `
                <div class="payment-info">
                    <div class="payment-name">${payment.name}</div>
                    <div class="payment-date">Vence: ${payment.dueDate}</div>
                </div>
                <div class="payment-amount">${payment.amount}</div>
            `;
            
            paymentsContainer.appendChild(paymentEl);
        });
        
        // Añadir el total
        const totalEl = document.createElement('div');
        totalEl.className = 'payment-total';
        totalEl.innerHTML = `
            <span class="total-label">Total Pendiente:</span>
            <span class="total-amount">$${totalAmount.toFixed(2)}</span>
        `;
        
        paymentsContainer.appendChild(totalEl);
    }
    
    // Función para renderizar historial de accesos
    function renderAccessHistory(history) {
        const historyContainer = document.getElementById('access-history');
        historyContainer.innerHTML = '';
        
        history.forEach(entry => {
            const entryEl = document.createElement('div');
            entryEl.className = 'access-item';
            
            entryEl.innerHTML = `
                <div class="access-date">${entry.date}</div>
                <div class="access-time">${entry.time}</div>
                <div class="access-type access-${entry.type === 'entry' ? 'entry' : 'exit'}">
                    ${entry.type === 'entry' ? 'Entrada' : 'Salida'}
                </div>
            `;
            
            historyContainer.appendChild(entryEl);
        });
    }
    
    // Event Listeners
    
    // Toggle menú móvil
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
        });
    }
    
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
        });
    }
    
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
        });
    }
    
    // Botón de editar estudiante
    if (editStudentBtn) {
        editStudentBtn.addEventListener('click', function() {
            console.log('Editar estudiante', studentId);
            // Aquí iría la lógica para abrir el modal de edición o redireccionar
            // a la página de edición
        });
    }
    
    // Inicializar la página
    loadStudentData(studentId);
});
