@extends('layouts.app')

@section('title', 'Transacciones')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>Transacciones</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('transactions.transaction-list', [
                'transactions' => $transactions,
                'accounts' => $accounts,
                'categories' => $categories
            ])
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/modules/transactions.js') }}"></script>
@endsection
