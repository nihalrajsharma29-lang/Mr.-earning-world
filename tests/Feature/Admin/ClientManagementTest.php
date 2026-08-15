<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_client_with_login_and_reset_email(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/clients', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '1234567890',
            'company' => 'Test Company',
            'address' => 'Test Address',
            'status' => 'Active',
            'commission_percentage' => 10,
            'create_user' => '1',
            'send_reset' => '1',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect(route('clients.index', absolute: false));
        $this->assertDatabaseHas('clients', ['email' => 'client@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'client@example.com', 'role' => 'client']);
    }
}
