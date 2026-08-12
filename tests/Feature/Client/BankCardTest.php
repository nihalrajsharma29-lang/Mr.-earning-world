<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_view_bank_card_form(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
        ]);

        Client::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '9999999999',
            'company' => 'Demo Company',
            'address' => 'Delhi',
            'status' => 'Active',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/client/bank-card');

        $response->assertOk();
        $response->assertSee('Bank Card');
        $response->assertSee('Account Number');
    }

    public function test_client_can_save_bank_details(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
        ]);

        Client::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '9999999999',
            'company' => 'Demo Company',
            'address' => 'Delhi',
            'status' => 'Active',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/client/bank-card', [
            'account_number' => '1234567890',
            'confirm_account_number' => '1234567890',
            'ifsc_code' => 'ICIC0001234',
            'bank_name' => 'ICICI Bank',
            'bank_address' => 'Gurugram, Haryana',
        ]);

        $response->assertRedirect('/client/bank-card');
        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'bank_account_number' => '1234567890',
            'bank_ifsc_code' => 'ICIC0001234',
            'bank_name' => 'ICICI Bank',
            'bank_address' => 'Gurugram, Haryana',
        ]);
    }
}
