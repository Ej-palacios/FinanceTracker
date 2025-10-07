<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Middleware\EnsureUserHasSettings;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsGoalsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ==================== Public Routes ====================
Route::get('/servicios', function () {
    return view('servicios');
})->name('servicios');

Route::get('/contacto', function () {
    return view('contacto');
})->name('contacto');

// ==================== Authentication Routes ====================
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// ==================== Protected Routes ====================
Route::middleware(['auth', EnsureUserHasSettings::class])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions - RUTAS COMPLETAS Y CORREGIDAS
    Route::prefix('transacciones')->group(function () {
        Route::get('/', [TransactionsController::class, 'index'])->name('transacciones.index');
        Route::get('/create', [TransactionsController::class, 'create'])->name('transacciones.create');
        Route::post('/', [TransactionsController::class, 'store'])->name('transacciones.store');
        Route::get('/{transaction}', [TransactionsController::class, 'show'])->name('transacciones.show');
        Route::get('/{transaction}/edit', [TransactionsController::class, 'edit'])->name('transacciones.edit');
        Route::put('/{transaction}', [TransactionsController::class, 'update'])->name('transacciones.update');
        Route::delete('/{transaction}', [TransactionsController::class, 'destroy'])->name('transacciones.destroy');
        // Ruta temporal para debug
Route::get('/exchanges-debug', function () {
    return view('exchanges.debug');
})->name('exchanges.debug');
    });

    // Reports
    Route::get('/reportes', [ReportsController::class, 'index'])->name('reportes.index');

    // Savings Goals
    Route::resource('savings-goals', SavingsGoalsController::class);
    Route::post('savings-goals/{savingsGoal}/add-savings', [SavingsGoalsController::class, 'addSavings'])->name('savings-goals.addSavings');
    Route::post('savings-goals/{savingsGoal}/toggle-status', [SavingsGoalsController::class, 'toggleStatus'])->name('savings-goals.toggleStatus');

    // Profile
    Route::prefix('perfil')->group(function () {
        Route::get('/', [ProfileController::class, 'profile'])->name('perfil');
        Route::put('/', [ProfileController::class, 'updateProfile'])->name('perfil.update');
        Route::put('/preferencias', [ProfileController::class, 'updatePreferences'])->name('perfil.preferences.update');
        Route::post('/agregar-ahorros', [ProfileController::class, 'addSavings'])->name('perfil.addSavings');
        Route::post('/liberar-ahorros', [ProfileController::class, 'releaseSavings'])->name('perfil.releaseSavings');
    });

    // Categories
    Route::controller(CategoryController::class)->group(function () {
        Route::post('/categories', 'store')->name('categories.store');
        Route::put('/categories/{category}', 'update')->name('categories.update');
        Route::delete('/categories/{category}', 'destroy')->name('categories.destroy');
    });

    // Accounts
    Route::controller(AccountController::class)->group(function () {
        Route::post('/accounts', 'store')->name('accounts.store');
        Route::put('/accounts/{account}', 'update')->name('accounts.update');
        Route::delete('/accounts/{account}', 'destroy')->name('accounts.destroy');
    });

    // Budgets
    Route::controller(BudgetController::class)->group(function () {
        Route::post('/budgets', 'store')->name('budgets.store');
        Route::put('/budgets/{budget}', 'update')->name('budgets.update');
        Route::delete('/budgets/{budget}', 'destroy')->name('budgets.destroy');
    });

    // ==================== API Routes ====================
    Route::prefix('api')->name('api.')->group(function () {
        // Transactions API
        Route::controller(TransactionsController::class)->group(function () {
            Route::get('/transacciones', 'index')->name('transacciones.index');
            Route::get('/transactions', 'getTransactions')->name('transactions.list');
        });

        // Categories API
        Route::get('/categories', [CategoryController::class, 'getCategories'])
            ->name('categories.list');

        // Accounts API
        Route::get('/accounts', [AccountController::class, 'getAccounts'])
            ->name('accounts.list');

        // Reports API
        Route::get('/monthly-summary', [ReportsController::class, 'monthlySummary'])
            ->name('monthly.summary');

        // Budgets API
        Route::get('/budgets', [BudgetController::class, 'getBudgets'])
            ->name('budgets.list');
    });
});

// === Rutas de Fallback ===
Route::fallback(function () {
    /** @var \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard $auth */
    $auth = auth();
    return $auth->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->middleware('auth');

// Deposits
Route::prefix('depositos')->group(function () {
    Route::get('/', [ExchangeController::class, 'index'])->name('deposits.index');
    Route::get('/crear', [ExchangeController::class, 'create'])->name('deposits.create');
    Route::post('/', [ExchangeController::class, 'store'])->name('deposits.store');
    Route::get('/{exchange}', [ExchangeController::class, 'show'])->name('deposits.show');
    Route::post('/{exchange}/aprobar', [ExchangeController::class, 'approve'])->name('deposits.approve');
    Route::post('/{exchange}/rechazar', [ExchangeController::class, 'reject'])->name('deposits.reject');
    Route::post('/calcular', [ExchangeController::class, 'calculateDeposit'])->name('deposits.calculate');
    Route::get('/buscar-usuarios', [ExchangeController::class, 'searchUsers'])->name('deposits.search');
});

// Notifications
Route::prefix('notificaciones')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/{notification}/leer', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/marcar-todas-leidas', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/contador-no-leidas', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::get('/lista', [NotificationController::class, 'getNotifications'])->name('notifications.list');
});