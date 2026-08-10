<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Customer;
use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DailyReportImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading
{
    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */

    /**
     * When importing from admin, rows may belong to different clients.
     * We detect client per-row by checking existing customer->client_id
     * or an optional `client_id`/`clientid` column in the sheet.
     */
    private ?int $clientId = null;

    public function __construct(?int $clientId = null)
    {
        $this->clientId = $clientId;
    }

    /*
    |--------------------------------------------------------------------------
    | Import Row
    |--------------------------------------------------------------------------
    */

    public function model(array $row)
    {
        $row = $this->normalizeRowKeys($row);
        /*
        |--------------------------------------------------------------------------
        | Host ID
        |--------------------------------------------------------------------------
        */

        $hostId = trim((string) ($this->value(
            $row,
            ['hostid', 'host_id', 'host id', 'host']
        ) ?? ''));

        if ($hostId === '') {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Report Date
        |--------------------------------------------------------------------------
        */

        $reportDateValue = $this->value(
            $row,
            ['dt', 'date', 'report_date', 'report date', 'reportdate']
        );

        if ($reportDateValue === null) {
            return null;
        }

        try {

            $reportDate = $this->date($reportDateValue);

        } catch (\Throwable $e) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Find / Create Customer
        |--------------------------------------------------------------------------
        */

        // Try to find existing customer first
        $customer = Customer::where('customer_id', $hostId)->first();
        $detectedClientId = $this->resolveClientId($row);

        if (! $detectedClientId && $this->clientId) {
            $detectedClientId = $this->clientId;
        }

        if (! $customer) {
            if (! $detectedClientId) {
                return null; // Cannot import a host without a known client.
            }

            $customer = Customer::create([
                'customer_id' => $hostId,
                'client_id' => $detectedClientId,
                'name' => $this->value($row, 'username') ?? $hostId,
                'username' => $this->value($row, 'username') ?? $hostId,
                'category' => $this->value($row, 'category'),
                'status' => 'Active',
                'approval_status' => 'approved',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Connect Existing Customer To Client
        |--------------------------------------------------------------------------
        */

        if (empty($customer->client_id) && $detectedClientId) {
            $customer->update(['client_id' => $detectedClientId]);
        }


        /*
        |--------------------------------------------------------------------------
        | Daily Report
        |--------------------------------------------------------------------------
        |
        | Same host + same date:
        | update existing report.
        |
        | New host + date:
        | create new report.
        |
        */

        DailyReport::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'dt' => $reportDate,
            ],
            [

                'client_id' => $customer->client_id,

                'host_id' => $hostId,

                'group_name' =>
                    $this->value($row, 'groupname'),

                'user_name' =>
                    $this->value($row, 'username'),

                'story_status' =>
                    $this->value($row, 'storystatus'),


                /*
                |--------------------------------------------------------------------------
                | Coins
                |--------------------------------------------------------------------------
                */

                'gift_coins' =>
                    $this->number(
                        $row['giftcoins'] ?? 0
                    ),

                'non_friend_video_coins' =>
                    $this->number(
                        $row['nonfriendvideocoins'] ?? 0
                    ),

                'friend_video_coins' =>
                    $this->number(
                        $row['friendvideocoins'] ?? 0
                    ),

                'task_coins' =>
                    $this->number(
                        $row['taskcoins'] ?? 0
                    ),

                'box_coins' =>
                    $this->number(
                        $row['boxcoins'] ?? 0
                    ),

                'total_coins' =>
                    $this->number(
                        $row['totalcoins'] ?? 0
                    ),


                /*
                |--------------------------------------------------------------------------
                | Group
                |--------------------------------------------------------------------------
                */

                'group_time' =>
                    $this->dateTime(
                        $row['grouptime'] ?? null
                    ),


                /*
                |--------------------------------------------------------------------------
                | Match
                |--------------------------------------------------------------------------
                */

                'match_count' =>
                    $this->number(
                        $row['matchcount'] ?? 0
                    ),

                'match_duration_min' =>
                    $this->decimal(
                        $row['matchdurationmin'] ?? 0
                    ),


                /*
                |--------------------------------------------------------------------------
                | KYC / Profile
                |--------------------------------------------------------------------------
                */

                'app_kyc_pass' =>
                    $this->value(
                        $row,
                        'appkycpass'
                    ),

                'profile_video_status' =>
                    $this->value(
                        $row,
                        'profilevideostatus'
                    ),

                'category' =>
                    $this->value(
                        $row,
                        'category'
                    ),


                /*
                |--------------------------------------------------------------------------
                | Call Performance
                |--------------------------------------------------------------------------
                */

                'long_call_ratio' =>
                    $this->decimal(
                        $row['longcallratio'] ?? 0
                    ),

                'avg_friend_call_duration_s30d' =>
                    $this->decimal(
                        $row['avgfriendcalldurations30d'] ?? 0
                    ),

                'total_call_duration_m' =>
                    $this->decimal(
                        $row['totalcalldurationm'] ?? 0
                    ),


                /*
                |--------------------------------------------------------------------------
                | Bank
                |--------------------------------------------------------------------------
                */

                'bank_country' =>
                    $this->value(
                        $row,
                        'bankcountry'
                    ),

                'if_bind_bank_info' =>
                    $this->value(
                        $row,
                        'ifbindbankinfo'
                    ),


                /*
                |--------------------------------------------------------------------------
                | Active
                |--------------------------------------------------------------------------
                */

                'if_active' =>
                    $this->value(
                        $row,
                        'ifactive'
                    ),


                /*
                |--------------------------------------------------------------------------
                | Weekly Coins
                |--------------------------------------------------------------------------
                */

                'current_week_total_coins' =>
                    $this->number(
                        $row['currentweektotalcoins'] ?? 0
                    ),

                'previous_week1_total_coins' =>
                    $this->number(
                        $row['previousweek1totalcoins'] ?? 0
                    ),

                'previous_week2_total_coins' =>
                    $this->number(
                        $row['previousweek2totalcoins'] ?? 0
                    ),

                'previous_week3_total_coins' =>
                    $this->number(
                        $row['previousweek3totalcoins'] ?? 0
                    ),


                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

                'payment_platform' =>
                    $this->value(
                        $row,
                        'paymentplatform'
                    ),


                /*
                |--------------------------------------------------------------------------
                | App
                |--------------------------------------------------------------------------
                */

                'app_id' =>
                    $this->value(
                        $row,
                        'appid'
                    ),


                /*
                |--------------------------------------------------------------------------
                | Live
                |--------------------------------------------------------------------------
                */

                'has_live_permission' =>
                    $this->boolean(
                        $row['haslivepermission'] ?? false
                    ),

                'start_live_duration_min' =>
                    $this->decimal(
                        $row['startlivedurationmin'] ?? 0
                    ),

                'live_to_call_ratio' =>
                    $this->decimal(
                        $row['livetocallratio'] ?? 0
                    ),
            ]
        );


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Excel ko ek saath memory me load nahi karega.
    | Har 500 rows ka chunk process hoga.
    |
    */

    public function chunkSize(): int
    {
        return 500;
    }


    /*
    |--------------------------------------------------------------------------
    | String Value
    |--------------------------------------------------------------------------
    */

    private function value(
        array $row,
        string|array $keys
    ): ?string {

        foreach ((array) $keys as $key) {
            $normalizedKey = $this->normalizeKey($key);

            if (
                array_key_exists($normalizedKey, $row) &&
                $row[$normalizedKey] !== null &&
                $row[$normalizedKey] !== ''
            ) {
                return trim((string) $row[$normalizedKey]);
            }
        }

        return null;
    }

    private function normalizeRowKeys(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $normalized[$this->normalizeKey($key)] = $value;
        }

        return $normalized;
    }

    private function normalizeKey(string $key): string
    {
        return strtolower(
            preg_replace('/[^a-z0-9]+/', '', $key)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Number
    |--------------------------------------------------------------------------
    */

    private function number($value): int
    {
        if (
            $value === null ||
            $value === ''
        ) {
            return 0;
        }

        return (int) round(
            (float) $value
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Decimal
    |--------------------------------------------------------------------------
    */

    private function decimal($value): float
    {
        if (
            $value === null ||
            $value === ''
        ) {
            return 0;
        }

        return (float) $value;
    }


    /*
    |--------------------------------------------------------------------------
    | Boolean
    |--------------------------------------------------------------------------
    */

    private function boolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(
            strtolower(
                trim((string) $value)
            ),
            [
                'true',
                'yes',
                '1',
                'on',
            ],
            true
        );
    }

    private function resolveClientId(array $row): ?int
    {
        $keys = [
            'client_id',
            'clientid',
            'clientuid',
            'client_uid',
            'client',
            'client name',
            'client_name',
        ];

        foreach ($keys as $key) {
            if ($this->value($row, $key)) {
                $value = trim((string) $this->value($row, $key));
                if (is_numeric($value)) {
                    return (int) $value;
                }

                $client = Client::where('name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->first();

                if ($client) {
                    return $client->id;
                }
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Date
    |--------------------------------------------------------------------------
    */

    private function date($value): string
    {
        if (
            $value === null ||
            $value === ''
        ) {
            throw new \Exception(
                'Invalid date'
            );
        }

        return Carbon::parse($value)
            ->format('Y-m-d');
    }


    /*
    |--------------------------------------------------------------------------
    | DateTime
    |--------------------------------------------------------------------------
    */

    private function dateTime($value): ?string
    {
        if (
            $value === null ||
            $value === ''
        ) {
            return null;
        }

        try {

            return Carbon::parse($value)
                ->format('Y-m-d H:i:s');

        } catch (\Throwable $e) {

            return null;
        }
    }
}