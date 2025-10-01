@extends('layouts.app')

@section('title', 'Reportes')
<link href="/assets/css/components.css" rel="stylesheet">

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>Reportes Financieros</h1>
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
</script>

@endsection