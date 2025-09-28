<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in('Feature'); // 👈 reuse Laravel traits

it('can show wallet balance', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/wallet');

        $response->assertOk()
             ->assertJsonStructure([
                 'user' => [
                    'id', 
                    'first_name', 
                    'last_name', 
                    'email'
                ],
                 'balance',
            ]);
});
