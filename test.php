<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = App\Http\Resources\UserProductResource::collection(App\Models\UserProduct::all());
echo json_encode(App\Utilities::ok($data)->getData());
