<?php

/**
 * Test: Webpay\Request.
 *
 * @author Pavel Jurásek
 * @package Webpay
 */

use JedenWeb\Webpay;

require_once __DIR__ . '/../bootstrap.php';


/*
$params = array(
    'OPERATION' => 'CREATE_ORDER',
    'ORDERNUMBER' => '126019299255',
    'MERORDERNUM' => '124759102034',
    'MD' => 'B8E5AD3CEBE760E95921FCBC4D92C7',
    'PRCODE' => 11,
    'SRCODE' => 0,
    'RESULTTEXT' => 'Unknown merchant',
);

$fp = fopen (__DIR__ . '/../crt/test_key.pem', 'r');
$key = fread ($fp, filesize(__DIR__ . '/../crt/test_key.pem'));
fclose ($fp);
$private = openssl_pkey_get_private($key, 'changeit');

$imploded = implode('|', $params);
openssl_sign($imploded, $signature, $private);
$params['DIGEST'] = base64_encode($signature);

openssl_sign($imploded.'|'.'0123456789', $signature, $private);
$params['DIGEST1'] = base64_encode($signature);

openssl_free_key($private);
file_put_contents(__DIR__. '/Response.fail.data', serialize($params));
*/

$params = unserialize(file_get_contents(__DIR__ . '/Response.fail.data'));

$response = new Webpay\Response(__DIR__ . '/../crt/test_cert.pem');
$response->setParams($params);

\Tester\Assert::false($response->verify());


$params = unserialize(file_get_contents(__DIR__ . '/Response.success.data'));

$response->setParams($params);

\Tester\Assert::true($response->verify('0123456789'));

\Tester\Assert::same($response->getOperation(), 'CREATE_ORDER');
\Tester\Assert::same($response->getWebpayOrder(), '126019299255');
\Tester\Assert::same($response->getMerchantOrder(), '124759102034');
\Tester\Assert::same($response->getMerchantData(), 'B8E5AD3CEBE760E95921FCBC4D92C7');
\Tester\Assert::same($response->getPRCode(), 0);
\Tester\Assert::same($response->getSRCode(), 0);
\Tester\Assert::same($response->getResultText(), 'OK');
