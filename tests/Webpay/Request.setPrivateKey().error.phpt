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
    $request = new Webpay\Request(__DIR__ . '/../crt/client.pem', 'wrong');
}, 'JedenWeb\Webpay\InvalidArgumentException');


\Tester\Assert::exception(function() {
    $request = new Webpay\Request(__DIR__ . '/../crt/malformed.pem', 'client');
}, 'JedenWeb\Webpay\InvalidArgumentException');
