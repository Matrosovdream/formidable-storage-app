<?php

use App\Models\User;

it('registers a user and persists to DB', function () {
    $payload = [
        'name'                  => 'Alice',
        'email'                 => 'alice@example.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response->assertOk()->assertJsonStructure(['user' => ['id', 'name', 'email']]);

    $this->assertDatabaseHas('users', [
        'email' => 'alice@example.com',
        'name'  => 'Alice',
    ]);

    $user = User::where('email', 'alice@example.com')->first();
    expect($user->password)->not->toBe('Password123!'); // hashed
});

it('rejects registration when email is missing', function () {
    $this->postJson('/api/register', ['name' => 'X'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

it('rejects duplicate email', function () {
    User::create([
        'name'     => 'Existing',
        'email'    => 'dup@example.com',
        'password' => bcrypt('secret'),
    ]);

    $this->postJson('/api/register', [
        'name'                  => 'Other',
        'email'                 => 'dup@example.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ])->assertStatus(422)->assertJsonValidationErrors(['email']);
});
