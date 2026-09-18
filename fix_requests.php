<?php
$reqs = [
    'app/Http/Requests/Provider/StoreProductRequest.php' => [
        "'name' => 'required|string|max:255',",
        "'description' => 'nullable|string',",
        "'price' => 'nullable|numeric',",
        "'address' => 'nullable|string',",
        "'latitude' => 'nullable|numeric',",
        "'longitude' => 'nullable|numeric',",
        "'product_id' => 'required|exists:products,id',"
    ],
    'app/Http/Requests/Provider/UpdateProductRequest.php' => [
        "'name' => 'string|max:255',",
        "'description' => 'nullable|string',",
        "'price' => 'nullable|numeric',",
        "'address' => 'nullable|string',",
        "'latitude' => 'nullable|numeric',",
        "'longitude' => 'nullable|numeric',",
        "'active' => 'boolean'"
    ],
    'app/Http/Requests/Provider/SendProductMessageRequest.php' => [
        "'message' => 'required|string',"
    ],
    'app/Http/Requests/User/SendProductMessageRequest.php' => [
        "'message' => 'required|string',",
        "'user_product_id' => 'required_without:request_id|exists:user_products,id',"
    ],
    'app/Http/Requests/User/SuggestLeadRequest.php' => [
        "'title' => 'required|string|max:255',",
        "'description' => 'nullable|string',",
        "'lead_type' => 'required|in:service,product',",
        "'category_text' => 'nullable|string',",
        "'address' => 'required|string',",
        "'latitude' => 'required|numeric',",
        "'longitude' => 'required|numeric',"
    ]
];

foreach ($reqs as $file => $rules) {
    $content = file_get_contents($file);
    $content = str_replace('return false;', 'return true;', $content);
    
    $ruleString = "return [\n            " . implode("\n            ", $rules) . "\n        ];";
    $content = preg_replace('/return \[.*\];/s', $ruleString, $content);
    
    file_put_contents($file, $content);
}
echo "Done";
