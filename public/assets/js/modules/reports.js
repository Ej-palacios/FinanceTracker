document.addEventListener('DOMContentLoaded', function() {
    // Configuración inicial de gráficos
    const charts = {
        monthlySummary: null,
        expense: null,
        category: null
    };
    

    // Inicializar todos los gráficos
    function initCharts() {
        initMonthlySummaryChart();
        initExpenseChart();
        initCategoryChart();
        setupEventListeners();
    }

    // 1. Gráfico de Resumen Mensual
    function initMonthlySummaryChart() {
        const ctx = document.getElementById('monthlySummaryChart');
        if (!ctx) return;

        charts.monthlySummary = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: window.monthlyLabels || [],
                datasets: [
                    {
                        label: 'Ingresos',
                        data: window.monthlyIncome || [],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Gastos',
                        data: window.monthlyExpenses || [],
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: getChartOptions('monthly')
        });
    }

    // 2. Gráfico de Distribución de Gastos (Pie)
    function initExpenseChart() {
        const ctx = document.getElementById('expenseChart');
        if (!ctx || !window.expenseChartData) return;

        charts.expense = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: window.expenseChartData.labels || [],
                datasets: [{
                    data: window.expenseChartData.data || [],
                    backgroundColor: window.expenseChartData.colors || [],
                    borderWidth: 1
                }]
            },
            options: getChartOptions('pie')
        });
    }


    // 3. Gráfico de Ingresos y Gastos por Categoría
// 3. Gráfico de Ingresos y Gastos por Categoría
function initCategoryChart() {
    const ctx = document.getElementById('categoryChart');
    if (!ctx || !window.categoryChartData) return;

    // Verificar datos recibidos
    console.log('Datos de ingresos:', window.categoryChartData.income);
    console.log('Datos de gastos:', window.categoryChartData.expenses);

    // Crear datasets separados con sus propias etiquetas
    const incomeDataset = {
        label: 'Ingresos',
        data: window.categoryChartData.income.data,
        backgroundColor: window.categoryChartData.income.colors,
        borderColor: window.categoryChartData.income.colors.map(c => c.replace('0.7', '1')),
        borderWidth: 1,
        // Asignar las etiquetas específicas para ingresos
        labels: window.categoryChartData.income.labels
    };

    const expenseDataset = {
        label: 'Gastos',
        data: window.categoryChartData.expenses.data.map(amount => -Math.abs(amount)),
        backgroundColor: window.categoryChartData.expenses.colors,
        borderColor: window.categoryChartData.expenses.colors.map(c => c.replace('0.7', '1')),
        borderWidth: 1,
        // Asignar las etiquetas específicas para gastos
        labels: window.categoryChartData.expenses.labels
    };

    // Configuración del gráfico
    charts.category = new Chart(ctx, {
        type: 'bar',
        data: {
            // Usar etiquetas vacías ya que manejaremos las etiquetas en el tooltip
            labels: Array(incomeDataset.data.length + expenseDataset.data.length).fill(''),
            datasets: [incomeDataset, expenseDataset]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = Math.abs(context.raw);
                            return `${context.dataset.label}: C$ ${value.toLocaleString()}`;
                        },
                        afterLabel: function(context) {
                            // Mostrar la etiqueta correcta según el dataset
                            return context.dataset.labels[context.dataIndex];
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 20,
                        usePointStyle: true
                    }
                }
            },
            scales: {
                x: {
                    stacked: false,
                    ticks: {
                        callback: function(value) {
                            return 'C$ ' + Math.abs(value).toLocaleString();
                        }
                    }
                },
                y: {
                    stacked: false,
                    grid: {
                        display: false
                    },
                    // Ocultar las etiquetas del eje Y ya que las mostramos en el tooltip
                    ticks: {
                        display: false
                    }
                }
            }
        }
    });
}
    // Función para obtener opciones de gráficos
    function getChartOptions(type) {
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || context.label || '';
                            const value = context.raw || 0;
                            return `${label}: C$ ${value.toLocaleString()}`;
                        }
                    }
                }
            }
        };

        switch (type) {
            case 'monthly':
                return {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'C$ ' + value.toLocaleString()
                            }
                        }
                    }
                };
            case 'pie':
                return {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 10
                            }
                        }
                    }
                };
            case 'bar':
                return {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'C$ ' + value.toLocaleString()
                            }
                        }
                    }
                };
            default:
                return commonOptions;
        }
    }

    // Configurar event listeners
function setupEventListeners() {
    // Selector de mes para Distribución de Gastos
    const expenseFilter = document.getElementById('expenseMonthFilter');
    if (expenseFilter) {
        expenseFilter.addEventListener('change', function() {
            updateFilter('expense', this.value);
        });
    }

    // Selector de mes para Gastos por Categoría
    const categoryFilter = document.getElementById('categoryMonthFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            updateFilter('category', this.value);
        });
    }
}

    // Actualizar filtros y recargar la página
function updateFilter(filterType, value = null) {
    const url = new URL(window.location.href);
    
    // Preservar los parámetros existentes del otro filtro
    const currentExpenseMonth = filterType === 'expense' ? value : url.searchParams.get('expense_month');
    const currentCategoryMonth = filterType === 'category' ? value : url.searchParams.get('category_month');
    
    // Limpiar todos los parámetros primero
    ['expense_month', 'category_month', 'expense_filter', 'category_filter'].forEach(param => {
        url.searchParams.delete(param);
    });

    // Establecer los parámetros correctamente
    if (filterType === 'expense') {
        url.searchParams.set('expense_month', value);
        url.searchParams.set('expense_filter', 'month');
        // Mantener el filtro de categoría si existe
        if (url.searchParams.get('category_month')) {
            url.searchParams.set('category_month', currentCategoryMonth);
            url.searchParams.set('category_filter', 'month');
        }
    } else if (filterType === 'category') {
        url.searchParams.set('category_month', value);
        url.searchParams.set('category_filter', 'month');
        // Mantener el filtro de gastos si existe
        if (url.searchParams.get('expense_month')) {
            url.searchParams.set('expense_month', currentExpenseMonth);
            url.searchParams.set('expense_filter', 'month');
        }
    }

    // Agregar parámetros de filtro para el otro gráfico si existen
    if (currentExpenseMonth && filterType !== 'expense') {
        url.searchParams.set('expense_month', currentExpenseMonth);
        url.searchParams.set('expense_filter', 'month');
    }
    
    if (currentCategoryMonth && filterType !== 'category') {
        url.searchParams.set('category_month', currentCategoryMonth);
        url.searchParams.set('category_filter', 'month');
    }

    window.location.href = url.toString();
}
    // Convertir a PDF
  const exportBtn = document.getElementById('exportPdfBtn');
    exportBtn?.addEventListener('click', async () => {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pdfWidth = 190;
        const pdfHeight = 80;
        let yOffset = 10;

        try {
            await new Promise(r => setTimeout(r, 500)); // Esperar gráficos

            const chartList = [
                { chart: charts.monthlySummary, title: 'Resumen Mensual' },
                { chart: charts.expense, title: 'Distribución de Gastos' },
                { chart: charts.category, title: 'Ingresos y Gastos por Categoría' }
            ];

            for (let i = 0; i < chartList.length; i++) {
                const item = chartList[i];
                if (!item.chart) continue;

                const imgData = item.chart.toBase64Image();
                pdf.setFontSize(14);
                pdf.text(item.title, 10, yOffset - 2);
                pdf.addImage(imgData, 'PNG', 10, yOffset, pdfWidth, pdfHeight);
                yOffset += pdfHeight + 10;

                if (i < chartList.length - 1 && yOffset + pdfHeight > pdf.internal.pageSize.getHeight()) {
                    pdf.addPage();
                    yOffset = 10;
                }
            }

            pdf.save('reporte.pdf');

        } catch (error) {
            console.error('Error generando PDF:', error);
            alert('Ocurrió un error al generar el PDF. Intenta recargando la página.');
        }
    });
    // Inicializar la aplicación
    initCharts();

   
});