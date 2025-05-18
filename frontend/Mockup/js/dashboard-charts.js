document.addEventListener("DOMContentLoaded", function () {
    // Gráfico de tendencia de accesos semanales
    const weeklyAccessCtx = document
        .getElementById("weeklyAccessChart")
        ?.getContext("2d");
    if (weeklyAccessCtx) {
        const weeklyAccessChart = new Chart(weeklyAccessCtx, {
            type: "line",
            data: {
                labels: [
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                ],
                datasets: [
                    {
                        label: "Semana Actual",
                        data: [320, 345, 375, 390, 310, 150],
                        backgroundColor: "rgba(52, 152, 219, 0.2)",
                        borderColor: "#3498db",
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: "#3498db",
                        pointBorderColor: "#3498db",
                        pointBorderWidth: 0,
                        fill: true,
                    },
                    {
                        label: "Semana Anterior",
                        data: [290, 325, 360, 375, 290, 125],
                        backgroundColor: "rgba(155, 89, 182, 0.2)",
                        borderColor: "#9b59b6",
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: "#9b59b6",
                        pointBorderColor: "#9b59b6",
                        pointBorderWidth: 0,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: "index",
                },
                plugins: {
                    legend: {
                        position: "top",
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 12,
                                family: "'Poppins', sans-serif",
                            },
                        },
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.7)",
                        titleColor: "#ffffff",
                        bodyColor: "#ffffff",
                        borderColor: "rgba(255, 255, 255, 0.2)",
                        borderWidth: 1,
                        cornerRadius: 8,
                        boxPadding: 6,
                        usePointStyle: true,
                        titleFont: {
                            size: 14,
                            weight: "bold",
                            family: "'Poppins', sans-serif",
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Poppins', sans-serif",
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(200, 200, 200, 0.2)",
                        },
                        title: {
                            display: true,
                            text: "Cantidad de Estudiantes",
                        },
                    },
                },
            },
        });
    }

    // Gráfico de accesos por hora (mejorado)
    const accessHoursCtx = document
        .getElementById("accessHoursChart")
        ?.getContext("2d");
    if (accessHoursCtx) {
        const accessHoursChart = new Chart(accessHoursCtx, {
            type: "bar",
            data: {
                labels: [
                    "6AM", "7AM", "8AM", "9AM", "10AM", "11AM", "12PM",
                    "1PM", "2PM", "3PM", "4PM", "5PM", "6PM", "7PM", "8PM",
                ],
                datasets: [
                    {
                        label: "Entradas",
                        data: [
                            5, 45, 35, 25, 20, 30, 45, 15, 35, 30, 25, 20, 15, 10, 5,
                        ],
                        backgroundColor: "rgba(52, 152, 219, 0.7)",
                        borderColor: "rgba(52, 152, 219, 1)",
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: "Salidas",
                        data: [2, 5, 15, 20, 15, 25, 40, 40, 20, 25, 30, 35, 25, 15, 8],
                        backgroundColor: "rgba(231, 76, 60, 0.7)",
                        borderColor: "rgba(231, 76, 60, 1)",
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "top",
                    },
                    tooltip: {
                        mode: "index",
                        intersect: false,
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        title: {
                            display: true,
                            text: "Hora del Día",
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Cantidad de Estudiantes",
                        },
                        grid: {
                            color: "rgba(200, 200, 200, 0.2)",
                        },
                    },
                },
            },
        });
    }

    // Gráfico de distribución de tipos de estudiantes - Comentado temporalmente
    /*
    const studentTypeCtx = document.getElementById('studentTypeChart')?.getContext('2d');
    if (studentTypeCtx) {
        const studentTypeChart = new Chart(studentTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Regulares', 'Nuevo Ingreso', 'Reingreso', 'Especiales'],
                datasets: [{
                    data: [65, 20, 10, 5],
                    backgroundColor: [
                        '#292d6b', // Color índigo para estudiantes regulares
                        'rgba(46, 204, 113, 0.8)', // Verde para nuevo ingreso
                        'rgba(52, 152, 219, 0.8)', // Azul para reingreso
                        'rgba(155, 89, 182, 0.8)' // Morado para especiales
                    ],
                    borderColor: [
                        '#292d6b',
                        'rgba(46, 204, 113, 1)',
                        'rgba(52, 152, 219, 1)',
                        'rgba(155, 89, 182, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return `${label}: ${percentage}% (${value} estudiantes)`;
                            }
                        }
                    }
                }
            }
        });
    }
    */

    // Gráfico de distribución por carreras - Comentado temporalmente
    /*
    const careerDistributionCtx = document.getElementById('careerDistributionChart')?.getContext('2d');
    if (careerDistributionCtx) {
        const careerDistributionChart = new Chart(careerDistributionCtx, {
            type: 'pie',
            data: {
                labels: ['Administración', 'Informática', 'Mercadeo', 'Contaduría', 'Turismo', 'Otras'],
                datasets: [{
                    data: [30, 25, 15, 20, 5, 5],
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.8)', // Azul
                        'rgba(46, 204, 113, 0.8)', // Verde
                        'rgba(155, 89, 182, 0.8)', // Morado
                        'rgba(241, 196, 15, 0.8)', // Amarillo
                        'rgba(230, 126, 34, 0.8)', // Naranja
                        'rgba(149, 165, 166, 0.8)'  // Gris
                    ],
                    borderColor: [
                        'rgba(52, 152, 219, 1)',
                        'rgba(46, 204, 113, 1)',
                        'rgba(155, 89, 182, 1)',
                        'rgba(241, 196, 15, 1)',
                        'rgba(230, 126, 34, 1)',
                        'rgba(149, 165, 166, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return `${label}: ${percentage}% (${value}%)`; // Muestra valor original en tooltip
                            }
                        }
                    }
                }
            }
        });
    }
    */
});
