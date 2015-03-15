<?php

/**
 * Test: Webpay\Request.
 *
 * @author Pavel Jurásek
 * @package Webpay
 */

use JedenWeb\Webpay;

require_once __DIR__ . '/../bootstrap.php';


\Tester\Assert::exception(function() {
    $request = new Webpay\Request(__DIR__ . '/../crt/client.pem', 'client');
    $request->setPayment(123.50, 'JPY');
}, 'JedenWeb\Webpay\InvalidArgumentException', "Unknown currency 'JPY'.");
