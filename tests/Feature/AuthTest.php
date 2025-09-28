<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('Auth', function () {
    it('can register a user', function () {
        $response = $this->postJson('/api/register', [
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'jane@example.com',
            'phone'      => '1234567890',
            'password'   => '123456',
            'password_confirmation' => '123456',
        ]);

        // dd($response->json(), $response->status());

        $response->assertStatus(201)
                 ->assertJsonStructure([
                    'user', 
                    'message'
                 ]);
    });

    it('can login a user', function () {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'message',
                 ]);
    });
});
