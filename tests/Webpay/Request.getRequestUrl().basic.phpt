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

$request->setWebPayUrl('https://test.3dsecure.gpwebpay.com/kb/order.do');
$request->setResponseUrl('http://localhost/response.php');
$request->setMerchantNumber('0123456789');
$request->setDepositFlag(TRUE);
$request->setOrderInfo('123123', '100001');
$request->setDescription('DESC');
$request->setMerchantData('DATA');
$request->setPayment(123.50);

$url = $request->getRequestUrl();

\Tester\Assert::same("https://test.3dsecure.gpwebpay.com/kb/order.do?MERCHANTNUMBER=0123456789&OPERATION=CREATE_ORDER&ORDERNUMBER=123123&AMOUNT=12350&CURRENCY=203&DEPOSITFLAG=1&MERORDERNUM=100001&URL=http%3A%2F%2Flocalhost%2Fresponse.php&DESCRIPTION=DESC&MD=DATA&DIGEST=WjhGr8y3V%2FvmrAs%2BJpl9DdvByIwhXZVFePhQpe9UiQgdjyRmAnK3UjqsbP%2FIDpAj2kNYQHiy0%2F4MK9kZPdX7kZZomJsRseAfZbxkjsURP8yQ5gDyatOwE5dckyVFXB0VsE9LXhcSgZxAcXBbRBIYSbMaPtjaA77Wb5stsXydYyG1EaHodlQY5L69%2FvOl7XWGkoVF3KYuyPsPmWcU6nW%2FXqYNhfXYeqkOKv6j8dUNIuJ7yhKiuu5eVA5uhSnlGCreuyIU4Kh7gQYJgRoyVwoSaZPcDlhkJLwLUtZgnha4Z%2BWzvlV0%2BF4QaPdAYlF1wj1we6y65Xa3npkHSoVADfJEjw%3D%3D", $url);
