<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use log mailer for testing
config(['mail.default' => 'log']);

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

echo "Preparing test user and sending reset link...\n";

$client = App\Models\Client::where('email', 'client1@example.test')->first();
if (! $client) {
    echo "Test client not found.\n";
    exit(1);
}

// Ensure a User exists
if (! $client->user) {
    $user = App\Models\User::create([
        'name' => $client->name,
        'email' => $client->email,
        'password' => Hash::make(\Illuminate\Support\Str::random(16)),
        'role' => 'client',
    ]);
    $client->user_id = $user->id;
    $client->save();
}

$email = $client->email;

$sent = Password::sendResetLink(['email' => $email]);

echo "Password::sendResetLink returned: ".json_encode($sent)."\n";

$log = file_exists(storage_path('logs/laravel.log')) ? file_get_contents(storage_path('logs/laravel.log')) : '';

echo "---- Last log lines ----\n";
echo implode("\n", array_slice(explode("\n", trim($log)), -20));
echo "\n---- End log ----\n";
