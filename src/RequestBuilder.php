<?php

namespace JedenWeb\Webpay;

/**
 * @author Pavel Jurásek
 */
class RequestBuilder
{

	/** @var string */
	private $privateKey;

	/** @var string */
	private $password;

	/** @var string */
	private $webpayUrl;

	/** @var string */
	private $merchantId;

	/** @var string */
	private $responseUrl;

	/** @var bool */
	private $depositFlag;

	/** @var mixed */
	private $webpayOrderNumber;

	/** @var mixed */
	private $merchantOrderNumber;

	/** @var int */
	private $price;

	/** @var string */
	private $currency;

	/**
	 * @param string $privateKey
	 * @param string $password
	 * @param string $webpayUrl
	 * @param string $merchantId
	 */
	public function __construct($privateKey, $password, $webpayUrl, $merchantId)
	{
		$this->privateKey = $privateKey;
		$this->password = $password;
		$this->webpayUrl = $webpayUrl;
		$this->merchantId = $merchantId;
	}

	/**
	 * @param string $responseUrl
	 */
	public function setResponseUrl($responseUrl)
	{
		$this->responseUrl = $responseUrl;
	}

	/**
	 * @param bool $depositFlag
	 */
	public function setDepositFlag($depositFlag)
	{
		$this->depositFlag = $depositFlag;
	}

	/**
	 * @param mixed $webpayOrderNumber
	 * @param mixed $merchantOrderNumber
	 */
	public function setOrderInfo($webpayOrderNumber, $merchantOrderNumber)
	{
		$this->webpayOrderNumber = $webpayOrderNumber;
		$this->merchantOrderNumber = $merchantOrderNumber;
	}

	/**
	 * @param float $price
	 * @param int $currency
	 */
	public function setPrice($price, $currency = Request::CZK)
	{
		$this->price = $price;
		$this->currency = $currency;
	}

	/**
	 * @return Request
	 */
	public function build()
	{
		$this->assertFilled();

		$request = new Request($this->privateKey, $this->password);
		$request->setWebPayUrl($this->webpayUrl);
		$request->setResponseUrl($this->responseUrl);
		$request->setMerchantNumber($this->merchantId);
		$request->setDepositFlag(TRUE);
		$request->setOrderInfo($this->webpayOrderNumber, $this->merchantOrderNumber);
		$request->setPayment($this->price, $this->currency);

		return $request;
	}

	private function assertFilled()
	{
		$msg = NULL;

		if ($this->webpayOrderNumber === NULL) {
			$msg = 'Webpay order number is missing.';
		} elseif ($this->merchantOrderNumber === NULL) {
			$msg = 'Merchant order number is missing.';
		} elseif ($this->responseUrl === NULL) {
			$msg = 'Return URL is missing.';
		} elseif ($this->price === NULL) {
			$msg = 'Price is missing.';
		}

		if ($msg !== NULL) {
			throw new InvalidStateException(__CLASS__ . ': Request cannot be built. '. $msg);
		}
	}

}
