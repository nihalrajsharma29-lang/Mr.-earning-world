<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reset_all_transfer_statuses(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $client1 = Client::create([
            'name' => 'Client One',
            'email' => 'client1@example.com',
            'phone' => '1111111111',
            'company' => 'Company One',
            'address' => 'Delhi',
            'status' => 'Active',
            'transfer_status' => 'transferred',
        ]);

        $client2 = Client::create([
            'name' => 'Client Two',
            'email' => 'client2@example.com',
            'phone' => '2222222222',
            'company' => 'Company Two',
            'address' => 'Noida',
            'status' => 'Active',
            'transfer_status' => 'transferred',
        ]);

        $response = $this->actingAs($admin)->post('/admin/bank-details/reset');

        $response->assertRedirect('/admin/bank-details');
        $response->assertSessionHas('success', 'All transfer statuses have been reset to pending.');

        $this->assertEquals('pending', $client1->fresh()->transfer_status);
        $this->assertEquals('pending', $client2->fresh()->transfer_status);
    }
}
