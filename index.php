<?php
require 'vendor/autoload.php';
use Omnipay\Omnipay;

// Create gateway instance
$gateway = Omnipay::create('Epayco');

// Configure gateway credentials
$gateway->setUsername('ePayco');
$gateway->setPkey('xxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
$gateway->setPrivatekey('xxxxxxxxxxxxxxxx');
$gateway->setPublicKey('xxxxxxxxxxxxxxxxxx');
$gateway->setLang('en'); //lang
$gateway->setTestMode(true); //false = production true= test
$gateway->setCheckoutMode('onpage'); // Options: 'onpage' or 'redirect'
```

### Processing a Payment

```php

// Transaction details
const currency = 'USD';
const cancelUrl = 'https://webhook.site/';
const returnUrl = 'https://webhook.site/';
const notifyUrl = 'https://webhook.site/';

$transactionId = '12341234';
$description = 'blue jean';
$firstName = 'jhon';
$lastName = 'doe';
$email = 'prueba@example.com';
$address = 'street 123';
$country = 'US';

// Build shopping cart
$cart = array(
    array(
        'name' => 'black shirt',
        'quantity' => 1,
        'type' => 'product',
        'price' => round(0, 2),
    ),
    array(
        'name' => 'blue jean',
        'quantity' => 1,
        'type' => 'product',
        'price' => round(20.0, 2),
    ),
    array(
        'name' => 'shoes',
        'quantity' => 1,
        'type' => 'product',
        'price' => round(0, 2),
    ),
    array(
        'name' => 'cap',
        'quantity' => 1,
        'type' => 'product',
        'price' => round(0, 2),
    ),
    array(
        'name' => 'Shipping Fee',
        'quantity' => 1,
        'type' => 'shipping',
        'price' => round(0, 2),
    ),
    array(
        'name' => 'Discount',
        'quantity' => 1,
        'type' => 'coupon',
        'price' => round(0, 2),
    ),
    array(
        'name' => 'Tax Fee',
        'type' => 'tax',
        'quantity' => 1,
        'price' => round(0, 2),
    ),
);

// Calculate totals
$product_price = 0;
$tax = 0;
$ico = 0;

foreach ($cart as $product) {
    if ($product['type'] == 'tax') {
        $tax += $product['price'];
    }
    if ($product['type'] == 'coupon') {
        $product_price -= $product['price'] * $product['quantity'];
    } else {
        $product_price += $product['price'] * $product['quantity'];
    }
}

$product_subtotal = $product_price - $tax;

// Set cart and process purchase
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
        'ipclient' => '0.0.0.0',
        'hascvv' => true,
        'extras' => [
            'extra1' => 'Extra Value 1',
            'extra2' => 'Extra Value 2',
            'extra3' => 'Extra Value 3',
            'extra4' => 'Extra Value 4',
        ],
    ]
)->send();

// Process response
if ($response->isRedirect()) {
    // Redirect to payment gateway
    $url = $response->getRedirectUrl();
    $data = $response->getRedirectData();
    
    // Output the redirect form HTML
    echo $response->getRedirectResponse();
    
} elseif ($response->isSuccessful()) {
    // Payment was successful
    echo 'Payment successful! Transaction reference: ' . $response->getTransactionReference();
    
} else {
    // Payment failed
    echo 'Payment failed: ' . $response->getMessage();
}