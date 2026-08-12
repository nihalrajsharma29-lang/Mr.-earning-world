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
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DailyReportImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading
{
    private int $importedRows = 0;
    private int $skippedRows = 0;

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
    private string $reportType = 'daily_report';

    public function __construct(?int $clientId = null, string $reportType = 'daily_report')
    {
        $this->clientId = $clientId;
        $this->reportType = $this->normalizeReportType($reportType);
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

        $hostId = $this->normalizeHostId((string) ($this->value(
            $row,
            ['hostid', 'host_id', 'host id', 'host', 'id']
        ) ?? ''));

        if ($hostId === '') {
            return $this->markSkipped();
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

        // Payment report sheets often provide Group Time instead of a Date column.
        if ($reportDateValue === null) {
            $reportDateValue = $this->value($row, ['grouptime', 'group time']);
        }

        // As a last fallback for payment report, keep the row importable.
        if ($reportDateValue === null && $this->reportType === 'payment_report') {
            $reportDateValue = now()->format('Y-m-d');
        }

        if ($reportDateValue === null) {
            return $this->markSkipped();
        }

        try {

            $reportDate = $this->date($reportDateValue);

        } catch (\Throwable $e) {

            return $this->markSkipped();
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

        if (! $customer && $this->reportType === 'payment_report') {
            return $this->markSkipped();
        }

        if (! $customer) {
            if (! $detectedClientId) {
                return $this->markSkipped(); // Cannot import a host without a known client.
            }

            $customer = Customer::create([
                'customer_id' => $hostId,
                'client_id' => $detectedClientId,
                'name' => $this->value($row, 'username') ?? $hostId,
                'username' => $this->value($row, 'username') ?? $hostId,
                'category' => $this->value($row, ['category', 'categories']),
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

        $country = $this->value($row, ['country', 'hostcountry', 'host country']);
        if (empty($customer->country) && $country) {
            $customer->update(['country' => $country]);
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
                'report_type' => $this->reportType,
            ],
            [

                'client_id' => $customer->client_id,
                'report_type' => $this->reportType,

                'host_id' => $hostId,

                'group_name' =>
                    $this->value($row, ['groupname', 'group']),

                'user_name' =>
                    $this->value($row, ['username', 'user name']),

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

                'salary_amount' =>
                    $this->decimal(
                        $row['salaryamount'] ?? $row['salary'] ?? 0
                    ),

                'salary_status' =>
                    $this->value(
                        $row,
                        ['salaystatus', 'salarystatus', 'salary_status', 'salary status']
                    ),

                'violation_records' =>
                    $this->value(
                        $row,
                        ['violationrecords', 'violation records', 'violations', 'violation']
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
                        ['category', 'categories']
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

                'weekly_total_coins_before_leftover' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'weeklytotalcoinsbefleftover',
                                'weekly total coins bef leftover',
                            ]
                        ) ?? 0
                    ),

                'weekly_final_coins_hosts' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'weeklyfinalcoinshosts',
                                'weekly final coinshosts',
                            ]
                        ) ?? 0
                    ),

                'weekly_reward_base_usd_before_strike_hosts' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'weeklyrewardbaseusdbeforestrikehosts',
                                'weekly reward baseusd before strike hosts',
                            ]
                        ) ?? 0
                    ),

                'screenshot_strike' =>
                    $this->number(
                        $this->value($row, ['screenshotstrike', 'screenshot strike']) ?? 0
                    ),

                'message_strike' =>
                    $this->number(
                        $this->value($row, ['messagestrike', 'message strike']) ?? 0
                    ),

                'weekly_reward_base_usd_hosts' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'weeklyrewardbaseusdhosts',
                                'weekly reward base usd hosts',
                            ]
                        ) ?? 0
                    ),

                'hosts_ranking_bonus_world_usd' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'hostsrankingbonusworldusd',
                                'hosts ranking bonus world usd',
                            ]
                        ) ?? 0
                    ),

                'hosts_ranking_bonus_country_usd' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'hostsrankingbonuscountryusd',
                                'hosts ranking bonus country usd',
                            ]
                        ) ?? 0
                    ),

                'br_co_bonus_usd' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'brcobonususd',
                                'br co bonus usd',
                            ]
                        ) ?? 0
                    ),

                'daily_rank_bonus_260803_260809' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'dailyrankbonus260803260809',
                                'daily rank bonus 260803260809',
                            ]
                        ) ?? 0
                    ),

                'hosts_final_reward_usd' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'hostsfinalrewardusd',
                                'hosts final reward usd',
                            ]
                        ) ?? 0
                    ),

                'agent_fee_usd' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'agentfeeusd',
                                'agent feeusd',
                            ]
                        ) ?? 0
                    ),

                'agent_one_time_bonus_usd' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'agentonetimebonususd',
                                'agent one time bonus usd',
                            ]
                        ) ?? 0
                    ),


                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

                'payment_platform' =>
                    $this->value(
                        $row,
                        [
                            'paymentplatform',
                            'platform',
                        ]
                    ),

                'payment_account_name_unique' =>
                    $this->value(
                        $row,
                        [
                            'isthepaymentaccountnameunique',
                            'paymentaccountnameunique',
                        ]
                    ),

                'if_phone_new' =>
                    $this->value(
                        $row,
                        [
                            'ifphonesnew',
                            'if phone new',
                        ]
                    ),

                'bind_payment_account' =>
                    $this->value(
                        $row,
                        [
                            'whethershebindthepaymentaccount',
                            'bindpaymentaccount',
                        ]
                    ),

                'has_been_host_before' =>
                    $this->value(
                        $row,
                        [
                            'whethershehasbeenhostbefore',
                            'hasbeenhostbefore',
                        ]
                    ),

                'reward_one_time_bonus_before_by_host_id' =>
                    $this->decimal(
                        $this->value(
                            $row,
                            [
                                'rewardonetimebonusbeforebyhostid',
                                'reward one time bonus before by host id',
                            ]
                        ) ?? 0
                    ),

                'average_call' =>
                    $this->decimal(
                        $this->value($row, ['averagecall', 'average call']) ?? 0
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

        $this->importedRows++;

        return null;
    }

    public function getImportedRows(): int
    {
        return $this->importedRows;
    }

    public function getSkippedRows(): int
    {
        return $this->skippedRows;
    }

    private function markSkipped()
    {
        $this->skippedRows++;

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

    private function normalizeHostId(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $value = str_replace([',', ' '], '', $value);

        // Excel often exports integer IDs as 12345.0
        if (preg_match('/^\d+\.0+$/', $value) === 1) {
            $value = strstr($value, '.', true);
        }

        return $value;
    }

    private function normalizeReportType(string $reportType): string
    {
        $reportType = strtolower(str_replace(' ', '_', trim($reportType)));

        return match ($reportType) {
            'payment_report',
            'payment_status',
            'violation_records' => $reportType,
            default => 'daily_report',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Number
    |--------------------------------------------------------------------------
    */

    private function number($value): int
    {
        $numericValue = $this->numeric($value);

        if ($numericValue === null) {
            return 0;
        }

        return (int) round(
            $numericValue
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Decimal
    |--------------------------------------------------------------------------
    */

    private function decimal($value): float
    {
        $numericValue = $this->numeric($value);

        if ($numericValue === null) {
            return 0;
        }

        return $numericValue;
    }

    private function numeric($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = trim((string) $value);
        $normalized = str_replace([',', '$', '₹', ' '], '', $normalized);

        // Keep digits, decimal dot and minus only.
        $normalized = preg_replace('/[^0-9.\-]/', '', $normalized);

        if ($normalized === '' || $normalized === '-' || $normalized === '.') {
            return null;
        }

        return is_numeric($normalized) ? (float) $normalized : null;
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

        // Fallback: if there is exactly one client in the system,
        // map imports to that client when sheet/client mapping is missing.
        if ($this->clientId) {
            return $this->clientId;
        }

        $clientCount = Client::count();
        if ($clientCount === 1) {
            $singleClient = Client::query()->select('id')->first();

            if ($singleClient) {
                return (int) $singleClient->id;
            }
        }

        // Practical fallback: some payment sheets do not contain client columns.
        // In that case, use the first client so rows are not skipped silently.
        if ($this->reportType === 'payment_report') {
            $fallbackClientId = Client::query()->orderBy('id')->value('id');
            if ($fallbackClientId) {
                return (int) $fallbackClientId;
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

        // Excel date serial fallback (common in imported xlsx/csv data).
        if (is_numeric($value)) {
            try {
                return Carbon::instance(
                    ExcelDate::excelToDateTimeObject((float) $value)
                )->format('Y-m-d');
            } catch (\Throwable $e) {
                // Continue with Carbon parse fallback below.
            }
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

        if (is_numeric($value)) {
            try {
                return Carbon::instance(
                    ExcelDate::excelToDateTimeObject((float) $value)
                )->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                // Continue with Carbon parse fallback below.
            }
        }

        try {

            return Carbon::parse($value)
                ->format('Y-m-d H:i:s');

        } catch (\Throwable $e) {

            return null;
        }
    }
}