<?php

namespace JedenWeb\Webpay;

class Request
{

    const CZK = 203;
    const EUR = 978;
    const USD = 840;

    /** @var array */
    private $params = array(
        'MERCHANTNUMBER' => NULL,
        'OPERATION' => 'CREATE_ORDER',
        'ORDERNUMBER' => NULL,
        'AMOUNT' => NULL,
        'CURRENCY' => self::CZK,
        'DEPOSITFLAG' => 1,
        'MERORDERNUM' => NULL,
        'URL' => NULL,
        'DESCRIPTION' => NULL,
        'MD' => NULL,
    );

    /** @var resource */
    private $privateKey;

    /** @var string  */
    private $webpayUrl;


    /**
     * @param string $file
     * @param string $passphrase
     */
    public function __construct($file, $passphrase)
    {
        $fp = fopen ($file, 'r');
        $key = fread ($fp, filesize($file));
        fclose ($fp);

        if (!($this->privateKey = openssl_pkey_get_private($key, $passphrase))) {
            throw new InvalidArgumentException("'$file' is not valid PEM private key (or passphrase is incorrect).");
        }
    }


    /**
     * @param mixed $webpayOrder
     * @param mixed $merchantOrder
     *
     * @return $this
     */
    public function setOrderInfo($webpayOrder, $merchantOrder)
    {
        $this->params['ORDERNUMBER'] = $webpayOrder;
        $this->params['MERORDERNUM'] = $merchantOrder;

        return $this;
    }


    /**
     * @param int $price
     * @param int $currency
     *
     * @return $this
     */
    public function setPayment($price, $currency = self::CZK)
    {
        $this->params['AMOUNT'] = $price * 100;
        $this->params['CURRENCY'] = $this->validateCurrency($currency);

        return $this;
    }


    /**
     * @param string $url
     *
     * @return $this
     */
    public function setWebpayUrl($url)
    {
        $this->webpayUrl = $url;

        return $this;
    }


    /**
     * @param string $responseUrl
     *
     * @return $this
     */
    public function setResponseUrl($responseUrl)
    {
        $this->params['URL'] = $responseUrl;

        return $this;
    }


    /**
     * @param mixed $merchantNumber
     *
     * @return $this
     */
    public function setMerchantNumber($merchantNumber)
    {
        $this->params['MERCHANTNUMBER'] = $merchantNumber;

        return $this;
    }


    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setDepositFlag($flag)
    {
        $this->params['DEPOSITFLAG'] = (int) $flag;

        return $this;
    }


    /**
     * @param string|NULL $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->params['DESCRIPTION'] = $description == NULL ? NULL : substr($description, 0, 255); // intentionally ==

        return $this;
    }


    /**
     * @param string|NULL $md
     *
     * @return $this
     */
    public function setMerchantData($md)
    {
        $this->params['MD'] = $md == NULL ? NULL : substr($md, 0, 255); // intentionally ==

        return $this;
    }


    /**
     * @return string
     */
    public function getRequestUrl()
    {
        if ($this->webpayUrl === NULL) {
            throw new InvalidStateException('Webpay URL not set.');
        }

        $this->sign();
        return $this->webpayUrl .'?'. http_build_query($this->params);
    }


    /**
     * @return void
     */
    private function sign()
    {
        $this->validateRequiredFields();
        $params = $this->filterOptionalFields($this->params);

        openssl_sign(implode('|', $params), $signature, $this->privateKey);

        $this->params['DIGEST'] = base64_encode($signature);
    }


    /**
     * @param mixed $currency
     *
     * @return mixed
     */
    private function validateCurrency($currency)
    {
        $allowed = array(
            self::CZK => 'CZK',
            self::EUR => 'EUR',
            self::USD => 'USD',
        );

        if (array_key_exists((int) $currency, $allowed)) {
            return $currency;
        }

        if (($key = array_search($currency, $allowed)) !== FALSE) {
            return $key;
        }

        throw new InvalidArgumentException("Unknown currency '$currency'.");
    }


    /**
     */
    private function validateRequiredFields()
    {
        $required = array(
            'MERCHANTNUMBER' => 1,
            'OPERATION' => 1,
            'ORDERNUMBER' => 1,
            'AMOUNT' => 1,
            'CURRENCY' => 1,
            'DEPOSITFLAG' => 1,
            'URL' => 1,
        );

        if (($key = array_search(NULL, array_intersect_key($this->params, $required), TRUE)) !== FALSE) {
            throw new InvalidStateException("Parameter $key is required but not set.");
        }
    }


    /**
     * @param array $params
     *
     * @return array
     */
    private function filterOptionalFields(array $params)
    {
        $optional = array(
            'MERORDERNUM',
            'DESCRIPTION',
            'MD',
        );

        foreach ($optional as $key) {
            if ($params[$key] === NULL) {
                unset($params[$key]);
            }
        }

        return $params;
    }

}
