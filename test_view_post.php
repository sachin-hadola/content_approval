<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'author@example.com')->first();
Auth::login($user);

$request = Illuminate\Http\Request::create('/posts/1', 'GET');
$response = $app->handle($request);
echo "View Post Status: " . $response->getStatusCode() . "\n";
