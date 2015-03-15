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
    $response = new Webpay\Response(__DIR__ . '/../crt/test_cert.pem');
    $response->setParams(array());
}, 'JedenWeb\Webpay\InvalidStateException', 'Parameter OPERATION is required but not present in query.');
