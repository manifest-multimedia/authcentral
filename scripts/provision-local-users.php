<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (! app()->environment('local') || config('app.url') !== 'https://authcentral.localhost') {
    throw new RuntimeException('Local AuthCentral only.');
}
$accounts = json_decode(file_get_contents($argv[1]), true, flags: JSON_THROW_ON_ERROR);
foreach ($accounts as $account) {
    if (App\Models\User::where('email', $account['email'])->exists()) {
        continue;
    }
    $user = App\Models\User::create(['name' => $account['name'], 'email' => $account['email'], 'password' => $account['password'], 'status' => 'active']);
    $user->forceFill(['email_verified_at' => now()])->save();
    $user->assignRole($account['role']);
}
echo "Local AuthCentral accounts provisioned.\n";
