document.addEventListener('DOMContentLoaded', function() {
    // Configuración inicial de gráficos
    const charts = {
        monthlySummary: null,
        expense: null,
        category: null
    };

    // Tasas de conversión estáticas para Nicaragua
    const conversionRates = {
        NIO: 1,       // Córdoba
        USD: 36.50,   // 1 USD = 36.50 C$
        EUR: 38.80    // 1 EUR = 38.80 C$
    };

    // Símbolos de moneda
    const currencySymbols = {
        NIO: 'C$',
        USD: '$',
        EUR: '€'
    };

    // Moneda seleccionada (puedes cambiar NIO, USD, EUR)
    let selectedCurrency = window.AppData?.currency || 'NIO';

    // Funciones de conversión y formato
    function convertCurrency(amount, from = 'NIO', to = selectedCurrency) {
        if (!conversionRates[from] || !conversionRates[to]) return amount;
        let amountInNIO = amount * conversionRates[from]; // Pasar a córdobas
        return amountInNIO / conversionRates[to];         // Convertir a destino
    }

    function formatCurrency(amount, currencyCode = selectedCurrency) {
        return `${currencySymbols[currencyCode]} ${amount.toFixed(2)}`;
    }

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
                labels: window.AppData.monthlyLabels || [],
                datasets: [
                    {
                        label: 'Ingresos',
                        data: (window.AppData.monthlyIncome || []).map(v => convertCurrency(v)),
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Gastos',
                        data: (window.AppData.monthlyExpenses || []).map(v => convertCurrency(v)),
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
        if (!ctx || !window.AppData.expenseChartData) return;

        charts.expense = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: window.AppData.expenseChartData.labels || [],
                datasets: [{
                    data: (window.AppData.expenseChartData.data || []).map(v => convertCurrency(v)),
                    backgroundColor: window.AppData.expenseChartData.colors || [],
                    borderWidth: 1
                }]
            },
            options: getChartOptions('pie')
        });
    }

    // 3. Gráfico de Ingresos y Gastos por Categoría
    function initCategoryChart() {
        const ctx = document.getElementById('categoryChart');
        if (!ctx || !window.AppData.categoryChartData) return;

        const incomeDataset = {
            label: 'Ingresos',
            data: (window.AppData.categoryChartData.income.data || []).map(v => convertCurrency(v)),
            backgroundColor: window.AppData.categoryChartData.income.colors || [],
            borderColor: (window.AppData.categoryChartData.income.colors || []).map(c => c.replace('0.7', '1')),
            borderWidth: 1,
            labels: window.AppData.categoryChartData.income.labels || []
        };

        const expenseDataset = {
            label: 'Gastos',
            data: (window.AppData.categoryChartData.expenses.data || []).map(v => -convertCurrency(Math.abs(v))),
            backgroundColor: window.AppData.categoryChartData.expenses.colors || [],
            borderColor: (window.AppData.categoryChartData.expenses.colors || []).map(c => c.replace('0.7', '1')),
            borderWidth: 1,
            labels: window.AppData.categoryChartData.expenses.labels || []
        };

        charts.category = new Chart(ctx, {
            type: 'bar',
            data: {
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
                                return `${context.dataset.label}: ${formatCurrency(value)}`;
                            },
                            afterLabel: function(context) {
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
                            callback: value => formatCurrency(Math.abs(value))
                        }
                    },
                    y: {
                        stacked: false,
                        grid: { display: false },
                        ticks: { display: false }
                    }
                }
            }
        });
    }

    // Función para obtener opciones comunes de gráficos
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
                            return `${label}: ${formatCurrency(value)}`;
                        }
                    }
                }
            }
        };

        switch (type) {
            case 'monthly':
            case 'bar':
                return {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: value => formatCurrency(value) }
                        }
                    }
                };
            case 'pie':
                return {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { position: 'right', labels: { boxWidth: 12, padding: 10 } }
                    }
                };
            default:
                return commonOptions;
        }
    }

    // Configurar eventos de selección de mes
    function setupEventListeners() {
        const expenseFilter = document.getElementById('expenseMonthFilter');
        if (expenseFilter) expenseFilter.addEventListener('change', function() {
            updateFilter('expense', this.value);
        });

        const categoryFilter = document.getElementById('categoryMonthFilter');
        if (categoryFilter) categoryFilter.addEventListener('change', function() {
            updateFilter('category', this.value);
        });
    }

    // Actualizar filtros y recargar página
    function updateFilter(filterType, value = null) {
        const url = new URL(window.location.href);
        const currentExpenseMonth = filterType === 'expense' ? value : url.searchParams.get('expense_month');
        const currentCategoryMonth = filterType === 'category' ? value : url.searchParams.get('category_month');

        ['expense_month', 'category_month', 'expense_filter', 'category_filter'].forEach(param => url.searchParams.delete(param));

        if (filterType === 'expense') {
            url.searchParams.set('expense_month', value);
            url.searchParams.set('expense_filter', 'month');
            if (currentCategoryMonth) {
                url.searchParams.set('category_month', currentCategoryMonth);
                url.searchParams.set('category_filter', 'month');
            }
        } else if (filterType === 'category') {
            url.searchParams.set('category_month', value);
            url.searchParams.set('category_filter', 'month');
            if (currentExpenseMonth) {
                url.searchParams.set('expense_month', currentExpenseMonth);
                url.searchParams.set('expense_filter', 'month');
            }
        }

        window.location.href = url.toString();
    }

    // Inicializar todo
    initCharts();
});