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
    $response = new Webpay\Response(__DIR__ . '/../crt/malformed_cert.pem');
}, 'JedenWeb\Webpay\InvalidArgumentException');
