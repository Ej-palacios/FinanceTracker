@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Resumen Financiero</h1>
        
        <div class="row">
            @include('components.dashboard.stats-widget', [
                'title' => 'Balance Actual',
                'value' => 'C$ ' . number_format($currentBalance, 2),
                'icon' => 'wallet',
                'color' => 'success'
            ])
            
            @include('components.dashboard.stats-widget', [
                'title' => 'Ingresos Mensuales',
                'value' => 'C$ ' . number_format($monthlyIncome, 2),
                'icon' => 'arrow-down-circle',
                'color' => 'info'
            ])
            
            @include('components.dashboard.stats-widget', [
                'title' => 'Gastos Mensuales',
                'value' => 'C$ ' . number_format($monthlyExpenses, 2),
                'icon' => 'arrow-up-circle',
                'color' => 'danger'
            ])
            
            @include('components.dashboard.stats-widget', [
                'title' => 'Ahorro Mensual',
                'value' => 'C$ ' . number_format($monthlyIncome - $monthlyExpenses, 2),
                'icon' => 'piggy-bank',
                'color' => 'warning',
                'footer' => ($monthlyIncome > 0 ? round(($monthlyIncome - $monthlyExpenses) / $monthlyIncome * 100, 2) : 0) . '% de tus ingresos',
                'footerIcon' => ($monthlyIncome > $monthlyExpenses) ? 'arrow-up' : 'arrow-down',
                'footerColor' => ($monthlyIncome > $monthlyExpenses) ? 'success' : 'danger'
            ])
        </div>
        
        <div class="row mt-4">
            <div class="col-md-8">
                @include('components.reports.monthly-summary', ['monthlySummary' => $monthlySummary])
            </div>
            <div class="col-md-4">
                @include('components.reports.expense-chart', ['monthlySummary' => $monthlySummary])
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                @include('components.dashboard.recent-transactions', ['transactions' => $recentTransactions])
            </div>
        </div>
    </div> <br><br> <br>
    <br>
    <br>
    <br>

@endsection