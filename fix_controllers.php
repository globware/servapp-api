<?php

// Provider/ProductController
$file = 'app/Http/Controllers/Provider/ProductController.php';
$c = file_get_contents($file);
$c = str_replace('use Illuminate\Http\Request;', "use Illuminate\Http\Request;\nuse App\Http\Requests\Provider\StoreProductRequest;\nuse App\Http\Requests\Provider\UpdateProductRequest;", $c);
$c = str_replace('public function store(Request $request)', 'public function store(StoreProductRequest $request)', $c);
$c = preg_replace('/\$validated = \$request->validate\(\[.*?\]\);/s', '$validated = $request->validated();', $c, 1);
$c = str_replace('public function update(Request $request, $id)', 'public function update(UpdateProductRequest $request, $id)', $c);
$c = preg_replace('/\$validated = \$request->validate\(\[.*?\]\);/s', '$validated = $request->validated();', $c, 1);
file_put_contents($file, $c);

// User/ScoutController
$file = 'app/Http/Controllers/User/ScoutController.php';
$c = file_get_contents($file);
$c = str_replace('use Illuminate\Http\Request;', "use Illuminate\Http\Request;\nuse App\Http\Requests\User\SuggestLeadRequest;", $c);
$c = str_replace('public function suggest(Request $request)', 'public function suggest(SuggestLeadRequest $request)', $c);
$c = preg_replace('/\$validated = \$request->validate\(\[.*?\]\);/s', '$validated = $request->validated();', $c, 1);
file_put_contents($file, $c);

// Provider/ProductRequestController
$file = 'app/Http/Controllers/Provider/ProductRequestController.php';
$c = file_get_contents($file);
$c = str_replace('use Illuminate\Http\Request;', "use Illuminate\Http\Request;\nuse App\Http\Requests\Provider\SendProductMessageRequest;", $c);
$c = str_replace('public function sendMessage(Request $request, $requestId)', 'public function sendMessage(SendProductMessageRequest $request, $requestId)', $c);
$c = preg_replace('/\$validated = \$request->validate\(\[.*?\]\);/s', '$validated = $request->validated();', $c, 1);
file_put_contents($file, $c);

// User/ProductRequestController
$file = 'app/Http/Controllers/User/ProductRequestController.php';
$c = file_get_contents($file);
$c = str_replace('use Illuminate\Http\Request;', "use Illuminate\Http\Request;\nuse App\Http\Requests\User\SendProductMessageRequest;", $c);
$c = str_replace('public function store(Request $request)', 'public function store(SendProductMessageRequest $request)', $c);
$c = str_replace('public function sendMessage(Request $request, $requestId)', 'public function sendMessage(SendProductMessageRequest $request, $requestId)', $c);
$c = preg_replace('/\$validated = \$request->validate\(\[.*?\]\);/s', '$validated = $request->validated();', $c);
file_put_contents($file, $c);

echo "Done";
