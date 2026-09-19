<?php

function addDocBlock($file, $method, $responseJson) {
    $c = file_get_contents($file);
    $docblock = "    /**\n     * @response 200 " . str_replace("\n", "\n     * ", $responseJson) . "\n     */\n    public function $method(";
    $c = preg_replace('/[ \t]*public function ' . $method . '\(/', "\n" . $docblock, $c);
    file_put_contents($file, $c);
}

$productResponse = '{
    "status": true,
    "message": "Success",
    "data": {
        "id": 1,
        "name": "Sample Product",
        "description": "Product Description",
        "price": 500,
        "address": "123 Street",
        "latitude": 9.0,
        "longitude": 7.0,
        "active": true,
        "is_unclaimed": false
    }
}';

$productListResponse = '{
    "status": true,
    "message": "Success",
    "data": [
        {
            "id": 1,
            "name": "Sample Product",
            "description": "Product Description",
            "price": 500,
            "address": "123 Street",
            "latitude": 9.0,
            "longitude": 7.0,
            "active": true,
            "is_unclaimed": false
        }
    ]
}';

$requestResponse = '{
    "status": true,
    "message": "Success",
    "data": {
        "id": 1,
        "user_id": 1,
        "user_product_id": 1,
        "Status": "inquiry_sent"
    }
}';

$requestListResponse = '{
    "status": true,
    "message": "Success",
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "user_product_id": 1,
            "Status": "inquiry_sent"
        }
    ]
}';

$chatResponse = '{
    "status": true,
    "message": "Success",
    "data": {
        "id": 1,
        "message": "Hello",
        "sender_id": 1
    }
}';

$chatListResponse = '{
    "status": true,
    "message": "Success",
    "data": {
        "status": "inquiry_sent",
        "chats": [
            {
                "id": 1,
                "message": "Hello",
                "sender_id": 1
            }
        ]
    }
}';

// User/ProductController
addDocBlock('app/Http/Controllers/User/ProductController.php', 'index', $productListResponse);
addDocBlock('app/Http/Controllers/User/ProductController.php', 'show', $productResponse);

// Provider/ProductController
addDocBlock('app/Http/Controllers/Provider/ProductController.php', 'index', $productListResponse);
addDocBlock('app/Http/Controllers/Provider/ProductController.php', 'store', $productResponse);
addDocBlock('app/Http/Controllers/Provider/ProductController.php', 'update', $productResponse);

// User/ProductRequestController
addDocBlock('app/Http/Controllers/User/ProductRequestController.php', 'index', $requestListResponse);
addDocBlock('app/Http/Controllers/User/ProductRequestController.php', 'show', $requestResponse);
addDocBlock('app/Http/Controllers/User/ProductRequestController.php', 'store', $requestResponse);
addDocBlock('app/Http/Controllers/User/ProductRequestController.php', 'getChats', $chatListResponse);
addDocBlock('app/Http/Controllers/User/ProductRequestController.php', 'sendMessage', $chatResponse);

// Provider/ProductRequestController
addDocBlock('app/Http/Controllers/Provider/ProductRequestController.php', 'index', $requestListResponse);
addDocBlock('app/Http/Controllers/Provider/ProductRequestController.php', 'show', $requestResponse);
addDocBlock('app/Http/Controllers/Provider/ProductRequestController.php', 'accept', $requestResponse);
addDocBlock('app/Http/Controllers/Provider/ProductRequestController.php', 'decline', $requestResponse);
addDocBlock('app/Http/Controllers/Provider/ProductRequestController.php', 'fulfill', $requestResponse);
addDocBlock('app/Http/Controllers/Provider/ProductRequestController.php', 'getChats', $chatListResponse);
addDocBlock('app/Http/Controllers/Provider/ProductRequestController.php', 'sendMessage', $chatResponse);

echo "Docblocks added.";
