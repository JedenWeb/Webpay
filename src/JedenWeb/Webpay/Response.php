<?php

namespace JedenWeb\Webpay;

class Response
{

    /** @var resource */
    private $publicKey;

    /** @var array */
    private $params = array();

    /** @var bool|NULL */
    private $authentic;


    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $fp = fopen($file, 'r');
        $key = fread($fp, filesize ($file));
        fclose ($fp);

        if (!($this->publicKey = openssl_get_publickey($key))) {
            throw new InvalidArgumentException("'$file' is not valid PEM public key (or passphrase is incorrect).");
        }
    }


    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->authentic = NULL;

        $this->params['OPERATION'] = isset($params['OPERATION']) ? $params['OPERATION'] : NULL;
        $this->params['ORDERNUMBER'] = isset($params['ORDERNUMBER']) ? $params['ORDERNUMBER'] : NULL;
        $this->params['MERORDERNUM'] = isset($params['MERORDERNUM']) ? $params['MERORDERNUM'] : NULL;
        $this->params['MD'] = isset($params['MD']) ? $params['MD'] : NULL;
        $this->params['PRCODE'] = isset($params['PRCODE']) ? $params['PRCODE'] : NULL;
        $this->params['SRCODE'] = isset($params['SRCODE']) ? $params['SRCODE'] : NULL;
        $this->params['RESULTTEXT'] = isset($params['RESULTTEXT']) ? $params['RESULTTEXT'] : NULL;

        $this->params['DIGEST'] = isset($params['DIGEST']) ? $params['DIGEST'] : NULL;
        $this->params['DIGEST1'] = isset($params['DIGEST1']) ? $params['DIGEST1'] : NULL;

        $this->validateRequiredFields();
    }


    /***/
    private function validateRequiredFields()
    {
        $required = array(
            'OPERATION' => 1,
            'ORDERNUMBER' => 1,
            'PRCODE' => 1,
            'SRCODE' => 1,
            'DIGEST' => 1,
            'DIGEST1' => 1,
        );

        if (($key = array_search(NULL, array_intersect_key($this->params, $required), TRUE)) !== FALSE) {
            throw new InvalidStateException("Parameter $key is required but not present in query.");
        }
    }


    /**
     * @param mixed|NULL $merchantNumber
     *
     * @return bool
     */
    public function authenticate($merchantNumber = NULL)
    {
        if ($this->authentic !== NULL) {
            return $this->authentic;
        }

        $params = $this->filterOptionalFields($this->params);
        $digest = $params['DIGEST'];
        $digest1 = $params['DIGEST1'];

        unset($params['DIGEST'], $params['DIGEST1']);

        $data = implode('|', $params);
        $authentic = openssl_verify($data, base64_decode($digest), $this->publicKey);

        if ($merchantNumber) {
            $data = $data .'|'. $merchantNumber;

            return $this->authentic = $authentic === 1 && openssl_verify($data, base64_decode($digest1), $this->publicKey) === 1;
        }

        return $this->authentic = $authentic === 1;
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
            'MD',
            'RESULTTEXT',
        );

        foreach ($optional as $key) {
            if ($params[$key] === NULL) {
                unset($params[$key]);
            }
        }

        return $params;
    }


    /**
     * @param mixed|NULL $merchantNumber
     *
     * @return bool
     */
    public function verify($merchantNumber = NULL)
    {
        if ($this->authentic === NULL) {
            $this->authenticate($merchantNumber);
        }

        return $this->authentic && $this->params['PRCODE'] == 0 && $this->params['SRCODE'] == 0; // intentionally ==
    }


    /**
     * @return mixed
     */
    public function getOperation()
    {
        return $this->params['OPERATION'];
    }


    /**
     * @return mixed
     */
    public function getWebpayOrder()
    {
        return $this->params['ORDERNUMBER'];
    }


    /**
     * @return mixed
     */
    public function getMerchantOrder()
    {
        return $this->params['MERORDERNUM'];
    }


    /**
     * @return mixed
     */
    public function getMerchantData()
    {
        return $this->params['MD'];
    }


    /**
     * @return mixed
     */
    public function getPRCode()
    {
        return $this->params['PRCODE'];
    }


    /**
     * @return mixed
     */
    public function getSRCode()
    {
        return $this->params['SRCODE'];
    }


    /**
     * @return string
     */
    public function getResultText()
    {
        return $this->params['RESULTTEXT'];
    }

}
