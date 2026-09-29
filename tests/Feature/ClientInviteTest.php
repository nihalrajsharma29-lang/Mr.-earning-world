<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ClientInviteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_client_form_has_a_copy_invite_link_button(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('clients.create'))
            ->assertOk()
            ->assertSee('Copy Invite Link')
            ->assertSee('client-invite?expires=', false);
    }

    public function test_signed_invite_can_create_a_client_and_login_account(): void
    {
        $inviteUrl = URL::temporarySignedRoute('client.invite.create', now()->addDay());

        $this->get($inviteUrl)
            ->assertOk()
            ->assertSee('Create your client account')
            ->assertSee('Confirm Password');

        $response = $this->post($inviteUrl, [
            'name' => 'Invited Client',
            'email' => 'invited@example.test',
            'phone' => '9876543210',
            'address' => 'New Delhi',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $response->assertOk()->assertSee('Account created');
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'name' => 'Invited Client',
            'email' => 'invited@example.test',
            'role' => 'client',
        ]);
        $this->assertDatabaseHas('clients', [
            'name' => 'Invited Client',
            'email' => 'invited@example.test',
            'phone' => '9876543210',
            'address' => 'New Delhi',
        ]);

        $client = Client::where('email', 'invited@example.test')->firstOrFail();
        $this->assertSame($client->user_id, User::where('email', $client->email)->value('id'));
        $this->assertTrue(Hash::check('SecurePass123', $client->user->password));
    }

    public function test_expired_invite_link_is_rejected(): void
    {
        $expiredUrl = URL::temporarySignedRoute('client.invite.create', now()->subMinute());

        $this->get($expiredUrl)->assertForbidden();
    }
}