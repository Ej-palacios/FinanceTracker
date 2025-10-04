@extends('layouts.app')

@section('title', 'Debug Exchange')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">🔍 DEBUG EXCHANGE SYSTEM</h5>
                </div>
                <div class="card-body">
                    @php
                        $currentUser = auth()->user();
                        $allUsers = \App\Models\User::all();
                        $allExchanges = \App\Models\ExchangeRequest::with(['fromUser', 'toUser'])->get();
                        $allAccounts = \App\Models\Account::all();
                        $allTransactions = \App\Models\Transaction::latest()->limit(10)->get();
                    @endphp

                    <div class="row">
                        <div class="col-md-4">
                            <h6>👥 USUARIOS</h6>
                            @foreach($allUsers as $user)
                            <div class="border p-2 mb-2">
                                <strong>ID:</strong> {{ $user->id }}<br>
                                <strong>Nombre:</strong> {{ $user->name }}<br>
                                <strong>User ID:</strong> {{ $user->user_id }}<br>
                                <strong>Email:</strong> {{ $user->email }}
                            </div>
                            @endforeach
                        </div>

                        <div class="col-md-4">
                            <h6>🔄 INTERCAMBIOS</h6>
                            @foreach($allExchanges as $exchange)
                            <div class="border p-2 mb-2">
                                <strong>ID:</strong> {{ $exchange->id }}<br>
                                <strong>De:</strong> {{ $exchange->fromUser->name }} ({{ $exchange->from_user_id }})<br>
                                <strong>Para:</strong> {{ $exchange->toUser->name }} ({{ $exchange->to_user_id }})<br>
                                <strong>Monto:</strong> {{ $exchange->from_amount }} {{ $exchange->from_currency }} → {{ $exchange->to_amount }} {{ $exchange->to_currency }}<br>
                                <strong>Estado:</strong> <span class="badge bg-{{ $exchange->status === 'pending' ? 'warning' : 'success' }}">{{ $exchange->status }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="col-md-4">
                            <h6>💰 CUENTAS</h6>
                            @foreach($allAccounts as $account)
                            <div class="border p-2 mb-2">
                                <strong>Usuario:</strong> {{ $account->user->name }}<br>
                                <strong>Cuenta:</strong> {{ $account->name }}<br>
                                <strong>Balance:</strong> {{ $account->balance }} {{ $account->currency }}
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>📊 ESTADÍSTICAS</h6>
                        <p><strong>Total Usuarios:</strong> {{ $allUsers->count() }}</p>
                        <p><strong>Total Intercambios:</strong> {{ $allExchanges->count() }}</p>
                        <p><strong>Total Cuentas:</strong> {{ $allAccounts->count() }}</p>
                        <p><strong>Total Transacciones:</strong> {{ $allTransactions->count() }}</p>
                        <p><strong>Usuario Actual:</strong> {{ $currentUser->name }} (ID: {{ $currentUser->id }})</p>
                    </div>

                    <!-- Formulario de prueba -->
                    <div class="mt-4">
                        <h6>🧪 TEST CREAR INTERCAMBIO</h6>
                        <form action="{{ route('exchanges.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Usuario Destino</label>
                                    <select name="to_user_id" class="form-control" required>
                                        @foreach($allUsers->where('id', '!=', $currentUser->id) as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->user_id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Monto</label>
                                    <input type="number" name="from_amount" class="form-control" value="100" required>
                                </div>
                                <div class="col-md-2">
                                    <label>De Moneda</label>
                                    <select name="from_currency" class="form-control" required>
                                        <option value="NIO">NIO</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>A Moneda</label>
                                    <select name="to_currency" class="form-control" required>
                                        <option value="NIO">NIO</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Crear Intercambio Test</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection