<?php

function replaceDocBlock($file, $method, $newDocblock) {
    $c = file_get_contents($file);
    // Remove the old @response block
    $c = preg_replace('/[ \t]*\/\*\*[\s\S]*?@response 200 \{[\s\S]*?\*\/[\n\r]*([ \t]*public function ' . $method . '\()/', "\n" . $newDocblock . "\n$1", $c);
    file_put_contents($file, $c);
}

// Provider/ProductController
$c = file_get_contents('app/Http/Controllers/Provider/ProductController.php');
$c = preg_replace('/[ \t]*\/\*\*[\s\S]*?@response 200 \{[\s\S]*?\*\//', '', $c); // Strip all old ones first
file_put_contents('app/Http/Controllers/Provider/ProductController.php', $c);

$c = file_get_contents('app/Http/Controllers/User/ProductRequestController.php');
$c = preg_replace('/[ \t]*\/\*\*[\s\S]*?@response 200 \{[\s\S]*?\*\//', '', $c);
file_put_contents('app/Http/Controllers/User/ProductRequestController.php', $c);

$c = file_get_contents('app/Http/Controllers/Provider/ProductRequestController.php');
$c = preg_replace('/[ \t]*\/\*\*[\s\S]*?@response 200 \{[\s\S]*?\*\//', '', $c);
file_put_contents('app/Http/Controllers/Provider/ProductRequestController.php', $c);

function addShape($file, $method, $shape) {
    $c = file_get_contents($file);
    $docblock = "    /**\n     * @return array{status: boolean, message: string, data: " . $shape . "}\n     */";
    $c = preg_replace('/([ \t]*public function ' . $method . '\()/', $docblock . "\n$1", $c, 1);
    file_put_contents($file, $c);
}

// Provider/ProductController
addShape('app/Http/Controllers/Provider/ProductController.php', 'index', 'array<\App\Http\Resources\UserProductResource>');
addShape('app/Http/Controllers/Provider/ProductController.php', 'store', '\App\Http\Resources\UserProductResource');
addShape('app/Http/Controllers/Provider/ProductController.php', 'update', '\App\Http\Resources\UserProductResource');

// User/ProductRequestController
addShape('app/Http/Controllers/User/ProductRequestController.php', 'index', 'array<\App\Http\Resources\UserProductRequestResource>');
addShape('app/Http/Controllers/User/ProductRequestController.php', 'show', '\App\Http\Resources\UserProductRequestResource');
addShape('app/Http/Controllers/User/ProductRequestController.php', 'store', '\App\Http\Resources\UserProductRequestResource');
addShape('app/Http/Controllers/User/ProductRequestController.php', 'sendMessage', '\App\Http\Resources\ChatResource');
addShape('app/Http/Controllers/User/ProductRequestController.php', 'getChats', 'array{status: string, chats: array<\App\Http\Resources\ChatResource>}');

// Provider/ProductRequestController
addShape('app/Http/Controllers/Provider/ProductRequestController.php', 'index', 'array<\App\Http\Resources\UserProductRequestResource>');
addShape('app/Http/Controllers/Provider/ProductRequestController.php', 'show', '\App\Http\Resources\UserProductRequestResource');
addShape('app/Http/Controllers/Provider/ProductRequestController.php', 'accept', '\App\Http\Resources\UserProductRequestResource');
addShape('app/Http/Controllers/Provider/ProductRequestController.php', 'decline', '\App\Http\Resources\UserProductRequestResource');
addShape('app/Http/Controllers/Provider/ProductRequestController.php', 'fulfill', '\App\Http\Resources\UserProductRequestResource');
addShape('app/Http/Controllers/Provider/ProductRequestController.php', 'sendMessage', '\App\Http\Resources\ChatResource');
addShape('app/Http/Controllers/Provider/ProductRequestController.php', 'getChats', 'array{status: string, chats: array<\App\Http\Resources\ChatResource>}');

echo "Shapes added.";
