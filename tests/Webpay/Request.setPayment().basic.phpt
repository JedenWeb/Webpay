<?php

/**
 * Test: Webpay\Request.
 *
 * @author Pavel Jurásek
 * @package Webpay
 */

use JedenWeb\Webpay;

require_once __DIR__ . '/../bootstrap.php';


$request = new Webpay\Request(__DIR__ . '/../crt/client.pem', 'client');
$request->setPayment(123.50, 'EUR');
$request->setPayment(123.50, Webpay\Request::EUR);

\Tester\Assert::true(TRUE);
