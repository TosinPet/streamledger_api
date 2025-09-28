<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns a paginated list of user transactions', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id]);

    Transaction::factory()->count(15)->create([
        'wallet_id' => $wallet->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/transactions');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'transactions' => [], // empty array = we don’t care about substructure
            ]);

    // Ensure only 10 are returned because of pagination
    expect($response->json('transactions.data'))->toHaveCount(10);
});

it('returns an empty list if no transactions exist', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/transactions');

    $response->assertStatus(200)
             ->assertJson([
                 'transactions' => [
                     'data' => [],
                 ],
             ]);
});

it('fails if user is not authenticated', function () {
    $response = $this->getJson('/api/v1/transactions');

    $response->assertStatus(401);
});
