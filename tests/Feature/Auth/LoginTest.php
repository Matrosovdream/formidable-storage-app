<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::create([
        'name'     => 'Bob',
        'email'    => 'bob@example.com',
        'password' => bcrypt('secret123'),
    ]);
});

it('logs in with valid credentials', function () {
    $this->postJson('/api/login', [
        'email'    => 'bob@example.com',
        'password' => 'secret123',
    ])->assertOk()->assertJsonPath('user.email', 'bob@example.com');
});

it('rejects invalid credentials', function () {
    $this->postJson('/api/login', [
        'email'    => 'bob@example.com',
        'password' => 'wrong',
    ])->assertStatus(422)->assertJsonPath('message', 'Invalid credentials.');
});

it('returns current user via /api/user when authenticated', function () {
    $this->actingAs($this->user)
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('email', 'bob@example.com');
});

it('logout endpoint responds ok when authenticated', function () {
    $this->actingAs($this->user, 'web')
        ->postJson('/api/logout')
        ->assertOk()
        ->assertJsonPath('message', 'Logged out');
});
