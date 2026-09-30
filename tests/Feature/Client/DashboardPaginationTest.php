<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_host_list_navigates_between_pages_without_changing_totals(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $client = Client::create([
            'name' => 'Pagination Client',
            'email' => $user->email,
            'phone' => '9999999991',
            'status' => 'Active',
            'user_id' => $user->id,
        ]);

        foreach (range(1, 11) as $number) {
            Customer::create([
                'client_id' => $client->id,
                'customer_id' => sprintf('host-%02d', $number),
                'name' => 'Host '.$number,
                'created_at' => now()->subSeconds($number),
            ]);
        }

        $this->actingAs($user)
            ->get(route('client.dashboard'))
            ->assertOk()
            ->assertSee('Total Hosts')
            ->assertSee('host-01')
            ->assertDontSee('host-11')
            ->assertSee('.content nav[role="navigation"] svg', false)
            ->assertSee(route('client.dashboard', ['page' => 2]), false);

        $this->get(route('client.dashboard', ['page' => 2]))
            ->assertOk()
            ->assertSee('Total Hosts')
            ->assertSee('host-11')
            ->assertDontSee('host-01');
    }
}