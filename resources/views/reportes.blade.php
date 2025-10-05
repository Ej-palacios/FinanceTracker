@extends('layouts.app')

@section('title', 'Reportes')
<link href="/assets/css/components.css" rel="stylesheet">

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Reportes Financieros</h1>

<!-- Botón para descargar PDF -->    
<div class="d-flex justify-content-end mb-3">
    <button id="exportPdfBtn" class="btn btn-danger shadow-sm d-flex align-items-center gap-2 px-4 py-2 custom-btn" style="background-color: #bb2d3b">
        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
        <span>PDF</span>
    </button>
</div>

<div id="reportContent">
    <!-- Todo tu contenido: gráficos, tablas, filtros, etc. -->
</div>

<style>
 .custom-btn {
        border-radius: 10px; /* semi cuadrado */
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .custom-btn:hover {
        background-color: #971c29 !important; /* rojo más oscuro al pasar el mouse */
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .custom-btn:active {
        transform: scale(0.97);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    }
</style>

            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Resumen Mensual -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resumen Mensual</h5>
                </div>
                <div class="card-body position-relative" style="height: 400px">
                    <div class="chart-container" style="position: absolute; width: 100%; height: 100%">
                        <canvas id="monthlySummaryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Distribución de Gastos (Pie Chart) -->
        <div class="col-md-4">
            <div class="card card--chart">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Distribución de Gastos</h5>
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="month" class="form-control" 
                               id="expenseMonthFilter"
                               value="{{ $expenseSelectedMonth ?? $currentMonth }}"
                               max="{{ $currentMonth }}">
                    </div>
                </div>
                <div class="card-body">
                    @if($expenseChart['isEmpty'])
                        <div class="alert alert-info text-center py-2">
                            No hay datos para el período seleccionado
                        </div>
                    @else
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Ingresos y Gastos por Categoría (Bar Chart) -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card--chart">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Ingresos y Gastos por Categoría</h5>
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="month" 
                               class="form-control" 
                               id="categoryMonthFilter"
                               value="{{ $categorySelectedMonth ?? $currentMonth }}"
                               max="{{ $currentMonth }}">
                    </div>
                </div>
                <div class="card-body">
                    @if($categoryChart['isEmpty'])
                        <div class="alert alert-info text-center py-2">
                            No hay datos para el período seleccionado
                        </div>
                    @else
                        <div class="chart-container" style="position: relative; height: 350px; width: 100%;">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="{{ asset('assets/js/modules/reports.js') }}"></script>

<script>
    window.AppData = {
        monthlyLabels: @json($monthlyLabels),
        monthlyIncome: @json($monthlyIncome),
        monthlyExpenses: @json($monthlyExpenses),
        expenseChartData: @json($expenseChart),
        categoryChartData: @json($categoryChart),
        currency: "{{ $userCurrency }}",      // <- Moneda del usuario (NIO, USD, EUR)
        currencySymbol: "{{ $currencySymbol }}" // <- Símbolo que se usará en gráficos
    };
 
    // Pasamos los datos PHP a JS como variables globales
    window.monthlyLabels = @json($monthlyLabels ?? []);
    window.monthlyIncome = @json($monthlyIncome ?? []);
    window.monthlyExpenses = @json($monthlyExpenses ?? []);
    window.expenseChartData = @json($expenseChart ?? []);
    
    // Nuevos datos para el gráfico combinado
    window.categoryChartData = {
        income: @json($categoryChart['income'] ?? []),
        expenses: @json($categoryChart['expenses'] ?? [])
    };
    
    window.currentMonth = @json($currentMonth);

</script>

@endsection