<?php
require 'vendor/autoload.php';
use Omnipay\Omnipay;

$gateway = Omnipay::create('Epayco');

$gateway->setUsername('ePayco');
$gateway->setPkey('xxxxxxxxxxxxxxxxxxxxxx');
$gateway->setPrivatekey('xxxxxxxxxxxxxxxxxxxxxxx');
$gateway->setPublicKey('xxxxxxxxxxxxxxxxxxxxxxx');
$gateway->setLang('es');
$gateway->setTestMode(false);
$gateway->setCheckoutMode('onpage'); // onpage or redirect

const currency = 'USD';
const cancelUrl = 'https://webhook.site/';
const returnUrl = 'https://webhook.site/';
const notifyUrl = 'https://webhook.site/';

$transactionId = '100087';
$description = 'Camisa Azul';
$firstName = 'john';
$lastName = 'doe';
$email = 'jhon@example.com';
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
        'country' => $country,
        'ipclient' => '127.0.0.2',
        'hascvv' => true,
        'extras' => [
            'extra1' => 'Extra Value 1',
            'extra2' => 'Extra Value 2',
            'extra3' => 'Extra Value 3',
            'extra4' => 'Extra Value 4',
        ],
        //'epaycopaymentmethoddisable' => [],
        //'cart' => $cart,
    ]
)->send();

// Process response
if ($response->isRedirect()) {
    $url = $response->getRedirectUrl();
    // for a form redirect, you can also call the following method:
    $data = $response->getRedirectData();
    // Redirect to offsite payment gateway
    //echo $response->redirect();
    // Or output the redirect form HTML
    echo $response->getRedirectResponse();

} elseif ($response->isSuccessful()) {
    // Payment was successful
    echo 'Payment successful! Transaction reference: ' . $response->getTransactionReference();      
} else {
    // Payment failed
    echo $response->getMessage();
}

