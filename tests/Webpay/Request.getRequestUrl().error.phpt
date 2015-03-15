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
    $request->getRequestUrl();
}, 'JedenWeb\Webpay\InvalidStateException', 'Webpay URL not set.');

\Tester\Assert::exception(function() {
    $request = new Webpay\Request(__DIR__ . '/../crt/client.pem', 'client');
    $request->setWebpayUrl('url');
    $request->getRequestUrl();
}, 'JedenWeb\Webpay\InvalidStateException', 'Parameter MERCHANTNUMBER is required but not set.');
