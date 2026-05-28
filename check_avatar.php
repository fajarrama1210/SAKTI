<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', '12345678@sakti.sch.id')->first();
if ($user) {
    echo 'Avatar path: ' . $user->avatar . PHP_EOL;
    echo 'Public URL: ' . Storage::disk('public')->url($user->avatar) . PHP_EOL;
    echo 'S3 URL: ' . Storage::disk('s3')->url($user->avatar) . PHP_EOL;
    echo 'Public exists: ' . (Storage::disk('public')->exists($user->avatar) ? 'yes' : 'no') . PHP_EOL;
    echo 'S3 exists: ' . (Storage::disk('s3')->exists($user->avatar) ? 'yes' : 'no') . PHP_EOL;
} else {
    echo 'User not found' . PHP_EOL;
}
