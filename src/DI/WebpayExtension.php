<?php

namespace JedenWeb\Webpay\DI;

use JedenWeb\Webpay\InvalidArgumentException;
use JedenWeb\Webpay\IRequestBuilderFactory;
use JedenWeb\Webpay\ResponseFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers;
use Nette\Utils\Validators;

class WebpayExtension extends CompilerExtension
{

	private $defaults = array(
		'webpayUrl' => 'https://3dsecure.gpwebpay.com/kb/order.do',
	);

	public function loadConfiguration()
	{
		$config = Helpers::merge($this->getConfig(), $this->defaults);

		Validators::assertField($config, 'privateKey', 'string');

		if (!file_exists($config['privateKey'])) {
			throw new InvalidArgumentException(sprintf('Private key file \'%s\' was not found.', $config['privateKey']));
		}

		Validators::assertField($config, 'publicKey', 'string');

		if (!file_exists($config['publicKey'])) {
			throw new InvalidArgumentException(sprintf('Public key file \'%s\' was not found.', $config['publicKey']));
		}

		Validators::assertField($config, 'password', 'string');
		Validators::assertField($config, 'merchantId', 'string');
		Validators::assertField($config, 'webpayUrl', 'string');

		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('requestBuilderFactory'))
			->setImplement(IRequestBuilderFactory::class)
			->setArguments(array(
				$config['privateKey'], $config['password'],
				$config['webpayUrl'], $config['merchantId'],
			));

		$container->addDefinition($this->prefix('responseFactory'))
			->setClass(ResponseFactory::class, array(
				$config['publicKey'],
			));
	}

}
