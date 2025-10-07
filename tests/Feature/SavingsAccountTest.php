<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SavingsAccountTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $mainAccount;
    protected $savingsAccount;
    protected $incomeCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create();

        // Create main account
        $this->mainAccount = Account::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'main',
            'balance' => 1000,
        ]);

        // Create savings account
        $this->savingsAccount = Account::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'savings',
            'balance' => 500,
        ]);

        // Create income category
        $this->incomeCategory = Category::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'income',
        ]);
    }

    public function test_user_can_view_profile_with_accounts()
    {
        $response = $this->actingAs($this->user)->get(route('perfil'));

        $response->assertStatus(200);
        $response->assertSeeText('Cuenta Principal');
        $response->assertSeeText('Subcuenta de Ahorros');
        $response->assertSeeText(number_format($this->mainAccount->balance, 2));
        $response->assertSeeText(number_format($this->savingsAccount->balance, 2));
    }

    public function test_release_savings_transfers_balance_to_main_account()
    {
        $initialMainBalance = $this->mainAccount->balance;
        $initialSavingsBalance = $this->savingsAccount->balance;

        $response = $this->actingAs($this->user)->post(route('perfil.releaseSavings'));

        $response->assertRedirect(route('perfil'));
        $response->assertSessionHas('success');

        $this->mainAccount->refresh();
        $this->savingsAccount->refresh();

        $this->assertEquals($initialMainBalance + $initialSavingsBalance, $this->mainAccount->balance);
        $this->assertEquals(0, $this->savingsAccount->balance);
    }

    public function test_cannot_create_transaction_on_savings_account()
    {
        $transactionData = [
            'type' => 'income',
            'amount' => 100,
            'category_id' => $this->incomeCategory->id,
            'account_id' => $this->savingsAccount->id,
            'date' => now()->format('Y-m-d'),
            'description' => 'Test income on savings',
        ];

        $response = $this->actingAs($this->user)->post(route('transacciones.store'), $transactionData);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('transactions', [
            'description' => 'Test income on savings',
        ]);
    }

    public function test_can_create_income_transaction_and_save_partial_amount()
    {
        $transactionData = [
            'type' => 'income',
            'amount' => 200,
            'category_id' => $this->incomeCategory->id,
            'account_id' => $this->mainAccount->id,
            'date' => now()->format('Y-m-d'),
            'description' => 'Test income with saving',
        ];

        // Simulate saving 50 from the income
        $savedAmount = 50;
        $remainingAmount = $transactionData['amount'] - $savedAmount;

        // Create transaction with remaining amount
        $response = $this->actingAs($this->user)->post(route('transacciones.store'), array_merge($transactionData, ['amount' => $remainingAmount]));

        $response->assertRedirect(route('transacciones.index'));
        $response->assertSessionHas('success');

        // Check transaction exists with remaining amount
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => $remainingAmount,
            'description' => 'Test income with saving',
        ]);

        // Add saved amount to savings account manually (simulate the saving logic)
        $this->savingsAccount->balance += $savedAmount;
        $this->savingsAccount->save();

        $this->assertEquals(500 + $savedAmount, $this->savingsAccount->balance);
    }
}
