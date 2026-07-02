<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('tenant scope does not recurse while the session guard resolves the current user', function () {
    $user = User::factory()->create();
    $sessionKey = Auth::guard('web')->getName();

    $response = $this
        ->withSession([$sessionKey => $user->getAuthIdentifier()])
        ->get(route('dashboard'));

    $response->assertOk();
});
