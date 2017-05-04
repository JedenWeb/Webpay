<?php

/**
 * Test: Webpay\DI\WebpayExtension.
 *
 * @author Pavel Jurásek
 * @package Webpay
 */

use JedenWeb\Webpay;

require_once __DIR__ . '/../../bootstrap.php';

$compiler = new \Nette\DI\Compiler();
$compiler->addExtension('webpay', new Webpay\DI\WebpayExtension);

Assert::exception(function () use ($compiler) {
	$compiler->compile();
}, \Nette\Utils\AssertionException::class, "Missing item 'privateKey' in array.");

$compiler->addConfig(array(
	'webpay' => array(
		'privateKey' => 'nonexistent',
	),
));

Assert::exception(function () use ($compiler) {
	$compiler->compile();
}, Webpay\InvalidArgumentException::class, "Private key file 'nonexistent' was not found.");

$compiler->addConfig(array(
	'webpay' => array(
		'privateKey' => __DIR__ . '/../../crt/test_key.pem',
	),
));

Assert::exception(function () use ($compiler) {
	$compiler->compile();
}, \Nette\Utils\AssertionException::class, "Missing item 'publicKey' in array.");

$compiler->addConfig(array(
	'webpay' => array(
		'privateKey' => __DIR__ . '/../../crt/client.pem',
		'publicKey' => __DIR__ . '/../../crt/test_cert.pem',
		'password' => 'client',
		'merchantId' => '0123456789',
	),
));

/** @var \Nette\DI\Container $container */
$container = createContainer($compiler);

/** @var Webpay\IRequestBuilderFactory $factory */
$factory = $container->getByType(Webpay\IRequestBuilderFactory::class);

Assert::type(Webpay\IRequestBuilderFactory::class, $factory);

$builder = $factory->create();

Assert::type(Webpay\RequestBuilder::class, $builder);

Assert::exception(function () use ($builder) {
	$builder->build();
}, Webpay\InvalidStateException::class, 'JedenWeb\Webpay\RequestBuilder: Request cannot be built. Webpay order number is missing.');

$builder->setOrderInfo('123', '234');
$builder->setResponseUrl('http://localhost');
$builder->setPrice(100); // = 1.00

$request = $builder->build();

Assert::type(Webpay\Request::class, $request);

Assert::true(\Nette\Utils\Strings::startsWith($request->getRequestUrl(), 'https://3dsecure.gpwebpay.com/kb/order.do?'));
