<?php

namespace JedenWeb\Webpay;

use Nette\Http\Request;

/**
 * @author Pavel Jurásek
 */
class ResponseFactory
{

	/** @var string */
	private $publicKey;

	/** @var Request */
	private $httpRequest;

	/**
	 * @param string $publicKey
	 */
	public function __construct($publicKey, Request $httpRequest)
	{
		$this->publicKey = $publicKey;
		$this->httpRequest = $httpRequest;
	}

	/**
	 * @return Response
	 */
	public function create()
	{
		$response = new Response($this->publicKey);
		$response->setParams($this->httpRequest->getQuery());

		return $response;
	}

}
