<?php
require 'vendor/autoload.php';
use Omnipay\Omnipay;

$gateway = Omnipay::create('Epayco');

$gateway->setUsername('613729');
$gateway->setPkey('0be55443eb875e0745a85c0cea13eced61b91f2a');
$gateway->setPublicKey('221b0d733d5ef3cd35501473390c81ee');
$gateway->setPrivateKey('0c3fb69a02a0ad6d1fed170a3b9dc081');
$gateway->setLang('en');
$gateway->setTestMode(false);

const currency = 'COP';
const cancelUrl = 'https://plugins.epayco.io/testing/prueba/index.html';
const returnUrl = 'https://plugins.epayco.io/testing/prueba/index.html';
const notifyUrl = 'https://plugins.epayco.io/';

$transactionId = '';
$description = 'Prueba pre-prod';
$firstName = 'john';
$lastName = 'doe';
$email = 'litapao.1621@gmail.com';
$address ='street # 123';
$country = 'CO';

$product_id = 123;
$cart = array();
$i = 0;
$cart[] = array(
    'name' => 'black shirt',
    'quantity' => 2,
    'type' => 'product',
    'price' => round(6.99, 2),
);
$cart[1] = array(
    'name' => 'blue jean',
    'quantity' => 1,
    'type' => 'product',
    'price' => round(12.99, 2),
);
$cart[] = array(
    'name' => 'Shipping Fee',
    'quantity' => 1,
    'type' => 'shipping',
    'price' => round(10005.99, 2),
);
$cart[] = array(
    'name' => 'Discount',
    'quantity' => 1,
    'type' => 'coupon',
    'price' => round(2.98, 2),
);
$cart[] = array(
    'name' => 'Tax Fee',
    'type' => 'tax',
    'quantity' => 1,
    'price' => round(1.02, 2),
);
$product_price=0;
$tax = 0;
$ico = 0;
foreach ($cart as $order_item_id => $product) {
    if($product['type'] == "tax"){
        $tax += $product['price'];
    }
    if($product['type'] == "coupon"){
        $product_price -= $product['price'] * $product['quantity'];
    }else{
        $product_price += $product['price'] * $product['quantity'];
    }
    $i++;
}
$product_subtotal = $product_price - $tax;
$gateway->setCart($cart);
$response = $gateway->purchase(
    [
        'amount' => $product_price,
        'subTotal' => $product_subtotal,
        'tax' => $tax,
        'ico' => $ico,
        'currency' => currency,
        'cancelUrl' => cancelUrl,
        'returnUrl' => returnUrl,
        'notifyUrl' => notifyUrl,
        'transactionId' => $transactionId,
        'description' => $description,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'address' => $address,
        'country' => $country
    ]
)->send();

// Process response
if ($response->isRedirect()) {
    $url = $response->getRedirectUrl();
// for a form redirect, you can also call the following method:
    $data = $response->getRedirectData();
    // Redirect to offsite payment gateway
    echo $response->redirect();
} else {
    // Payment failed
    echo $response->getMessage();
}

