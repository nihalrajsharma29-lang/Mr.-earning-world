<?php

namespace Tests\Feature;

use App\Exports\HostIdsExport;
use App\Models\Client;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class HostIdsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_manager_can_export_host_and_client_ids(): void
    {
        Excel::fake();

        $client = Client::create([
            'name' => 'Example Client',
            'email' => 'client@example.test',
            'phone' => '1234567890',
        ]);
        $host = Customer::create([
            'client_id' => $client->id,
            'customer_id' => 'HOST-42',
            'name' => 'Example Host',
        ]);

        foreach (['admin', 'manager'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route($role.'.hosts.export'))
                ->assertOk();
        }

        Excel::assertDownloaded('host-ids.xlsx', function (HostIdsExport $export) use ($client, $host): bool {
            $exportedHost = $export->query()->first();

            return $export->headings() === ['Host ID', 'Client ID', 'Client Name']
                && $export->query()->count() === 1
                && $export->map($exportedHost) === ['HOST-42', $client->id, 'Example Client'];
        });
    }
}