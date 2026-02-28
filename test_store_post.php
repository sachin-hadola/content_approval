<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'author@example.com')->first();
Auth::login($user);

session()->start();
$request = Illuminate\Http\Request::create('/posts', 'POST', [
    '_token' => csrf_token(),
    'title' => 'Browser Test Post',
    'body' => 'This is a test of the form submission',
]);
$request->setLaravelSession(session());

$response = $app->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Redirect: " . $response->headers->get('Location') . "\n";

if ($response->getStatusCode() == 302 && !$response->headers->get('Location')) {
    echo "Validation errors: \n";
    print_r(session()->get('errors') ? session()->get('errors')->all() : 'No errors flashed');
}

$latestPost = App\Models\Post::latest()->first();
echo "\nLatest Post DB Title: " . ($latestPost ? $latestPost->title : 'None');
