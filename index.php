<?php
require 'vendor/autoload.php';
use Omnipay\Omnipay;

$gateway = Omnipay::create('Epayco');

$gateway->setUsername('xxxx');
$gateway->setPkey('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
$gateway->setPublicKey('xxxxxxxxxxxxxxxxxx');
$gateway->setLang('en');
$gateway->setTestMode(true);

$cart = array();

$i = 0;

$product_id = 123;
$cart[] = array(
    'name' => 'camisa rosa',
    'quantity' => 2,
    'type' => 'product',
    'price' => round(6.99, 2),
);
$cart[1] = array(
    'name' => 'jean azul',
    'quantity' => 1,
    'type' => 'product',
    'price' => round(12.99, 2),
);
$cart[] = array(
    'name' => 'Shipping Fee',
    'quantity' => 1,
    'type' => 'shipping',
    'price' => round(5.99, 2),
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
        'ico' => 0,
        'currency' => 'USD',
        'cancelUrl' => 'www.sampĺe.cancel',
        'returnUrl' => 'www.sampĺe.return',
        'notifyUrl' => 'www.sample.norify',
        'transactionId' => '12341234',
        'description' => 'pago de prueba',
        'firstName' => 'Ricardo',
        'lastName' => 'Saldarriaga',
        'email' => 'ricardo.saldarriaga@epayco.com',
        'address' => 'calle 109 # 123',
        'country' => 'CO'
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

