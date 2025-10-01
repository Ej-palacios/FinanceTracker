@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Resumen Financiero ({{ $currencySymbol }})</h1>
    
    <div class="row">
        @include('components.dashboard.stats-widget', [
            'title' => 'Balance Actual',
            'value' => $currencySymbol . ' ' . number_format($currentBalance, 2, '.', ','),
            'icon' => 'wallet',
            'color' => 'success'
        ])
        
        @include('components.dashboard.stats-widget', [
            'title' => 'Ingresos Mensuales',
            'value' => $currencySymbol . ' ' . number_format($monthlyIncome, 2, '.', ','),
            'icon' => 'arrow-down-circle',
            'color' => 'info'
        ])
        
        @include('components.dashboard.stats-widget', [
            'title' => 'Gastos Mensuales',
            'value' => $currencySymbol . ' ' . number_format($monthlyExpenses, 2, '.', ','),
            'icon' => 'arrow-up-circle',
            'color' => 'danger'
        ])
        
        @include('components.dashboard.stats-widget', [
            'title' => 'Ahorro Mensual',
            'value' => $currencySymbol . ' ' . number_format($monthlyIncome - $monthlyExpenses, 2, '.', ','),
            'icon' => 'piggy-bank',
            'color' => 'warning',
            'footer' => ($monthlyIncome > 0 
                ? number_format((($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100, 2) 
                : 0) . '% de tus ingresos',
            'footerIcon' => ($monthlyIncome > $monthlyExpenses) ? 'arrow-up' : 'arrow-down',
            'footerColor' => ($monthlyIncome > $monthlyExpenses) ? 'success' : 'danger'
        ])
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            @include('components.dashboard.recent-transactions', ['transactions' => $recentTransactions])
        </div>
    </div>
</div>
@endsection