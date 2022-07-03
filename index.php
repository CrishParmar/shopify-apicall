<?php
include_once('includes/shopify.php');
include_once('includes/cannadropApi.php');

$shopify = new Shopify();
$shopify->set_url("kaileenw.myshopify.com");
$shopify->set_apikey("007397aefd15f138a437f3a396615148");
$shopify->set_password("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");


$cannadropApi = new cannadropApi();
$newproduct = $cannadropApi->canna_api();

foreach ($newproduct as $productkey => $product) {

    $products = $shopify->rest_api('/admin/api/2022-07/products.json', $product, 'POST');
    $products = json_decode($products['body'], true);
    echo print_r($products['id']);

}