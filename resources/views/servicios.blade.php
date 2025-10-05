@extends('layouts.app')

@section('title', 'Servicios - FinanceTracker')

@section('content')

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Hero Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="bi bi-gear-fill me-3"></i>Nuestros Servicios
                </h1>
                <p class="lead text-muted fs-5">
                    Herramientas financieras avanzadas para gestionar tu dinero de manera eficiente
                </p>
            </div>

            <!-- Services Grid -->
            <div class="row g-4">
                <!-- Gestión de Transacciones -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-card border-0 rounded-4">
                        <div class="card-body text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="bi bi-receipt-cutoff display-4 text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Gestión de Transacciones</h5>
                            <p class="card-text text-muted">
                                Registra y administra todas tus transacciones financieras de manera organizada.
                                Categoriza ingresos y gastos para un mejor control.
                            </p>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Registro de ingresos y gastos</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Categorización automática</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Historial completo</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Control de Presupuestos -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-card border-0 rounded-4">
                        <div class="card-body text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="bi bi-pie-chart-fill display-4 text-success"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Control de Presupuestos</h5>
                            <p class="card-text text-muted">
                                Establece límites de gasto por categoría y periodo para mantener tus finanzas bajo control.
                            </p>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Presupuestos mensuales/anuales</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Alertas de límite</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Reportes de cumplimiento</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Reportes y Análisis -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-card border-0 rounded-4">
                        <div class="card-body text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="bi bi-bar-chart-line-fill display-4 text-info"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Reportes y Análisis</h5>
                            <p class="card-text text-muted">
                                Visualiza tus finanzas con gráficos interactivos y reportes detallados.
                            </p>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Gráficos de tendencias</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Análisis mensual/anual</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Exportación de datos</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Gestión de Cuentas -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-card border-0 rounded-4">
                        <div class="card-body text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="bi bi-wallet-fill display-4 text-warning"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Gestión de Cuentas</h5>
                            <p class="card-text text-muted">
                                Administra múltiples cuentas bancarias y de efectivo desde un solo lugar.
                            </p>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Múltiples cuentas</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Balances actualizados</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Transferencias entre cuentas</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Intercambios Monetarios -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-card border-0 rounded-4">
                        <div class="card-body text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="bi bi-currency-exchange display-4 text-danger"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Depósitos a Usuarios</h5>
                            <p class="card-text text-muted">
                                Realiza depósitos a otros usuarios de forma segura.
                            </p>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Depósitos P2P</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Transferencias seguras</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Historial de transacciones</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Notificaciones -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-card border-0 rounded-4">
                        <div class="card-body text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="bi bi-bell-fill display-4 text-secondary"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Notificaciones Inteligentes</h5>
                            <p class="card-text text-muted">
                                Recibe alertas importantes sobre tu actividad financiera y límites de presupuesto.
                            </p>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Alertas de presupuesto</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Notificaciones de depósitos</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Recordatorios automáticos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


@endsection
