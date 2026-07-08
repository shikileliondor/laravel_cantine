<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_pin_code(): void
    {
        $user = User::factory()->create([
            'pin_code' => '1234',
        ]);

        $response = $this->postJson('/api/login/pin', [
            'pin_code' => '1234',
            'device_name' => 'borne-cantine',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Connexion par code PIN reussie.')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['token']);
    }

    public function test_pin_login_rejects_invalid_pin_code(): void
    {
        User::factory()->create([
            'pin_code' => '1234',
        ]);

        $response = $this->postJson('/api/login/pin', [
            'pin_code' => '9999',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'Code PIN invalide.');
    }
}
