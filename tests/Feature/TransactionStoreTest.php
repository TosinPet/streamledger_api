<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a credit transaction and increases balance', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100]);

    $payload = [
        'entry' => 'credit',
        'amount' => 50,
        'description' => 'Salary',
    ];

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/transactions', $payload);

    $response->assertStatus(201)
             ->assertJson([
                 'message' => 'Transaction created successfully.',
                 'transaction' => [
                     'entry' => 'credit',
                     'amount' => 50,
                     'description' => 'Salary',
                 ],
             ]);

    // Cast to float before assertion
    expect((float) $wallet->fresh()->balance)->toBe(150.0);
    expect(Transaction::count())->toBe(1);
});

it('creates a debit transaction and decreases balance', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 200]);

    $payload = [
        'entry' => 'debit',
        'amount' => 75,
        'description' => 'Shopping',
    ];

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/transactions', $payload);

    $response->assertStatus(201)
             ->assertJsonPath('transaction.entry', 'debit')
             ->assertJsonPath('transaction.amount', 75);

    expect((float) $wallet->fresh()->balance)->toBe(125.0);
    expect(Transaction::count())->toBe(1);
});

it('fails to create debit transaction if insufficient funds', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 30]);

    $payload = [
        'entry' => 'debit',
        'amount' => 100,
    ];

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/transactions', $payload);

    $response->assertStatus(500)
            ->assertJson([
                'message' => 'Insufficient funds.'
            ]);

    expect((float) $wallet->fresh()->balance)->toBe(30.0);
    expect(Transaction::count())->toBe(0);
});

it('fails if user is not authenticated', function () {
    $payload = [
        'entry' => 'credit',
        'amount' => 50,
    ];

    $response = $this->postJson('/api/v1/transactions', $payload);

    $response->assertStatus(401);
});