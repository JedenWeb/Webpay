Wrapper for GP Webpay request and response
===

## Sending request

```
  use JedenWeb\Webpay;

  $request = new Webpay\Request('private.pem', 'password');
  $request->setWebPayUrl('https://test.3dsecure.gpwebpay.com/rb/order.do');
  $request->setResponseUrl('http://example.com/order.php');
  $request->setMerchantNumber(1234);
  $request->setOrderInfo(100001 /* webpay order number */, 12345678 /* eshop order number */);
  $request->setPayment(10.50); // optionally Request::CZK, Request::EUR, Request::USD as second parameter, CZK is default
  echo "<a href='{$request->getRequestUrl()}'>Pay</a>";
```

In Nette
```
extensions:
	webpay: JedenWeb\Webpay\DI\WebpayExtension
	
webpay:
	privateKey: %appDir%/cert/private.pem
	publicKey: %appDir%/cert/public.pem
	password: 'abc'
	merchantId: '012345'
	# webpayUrl: 'https://3dsecure.gpwebpay.com/kb/order.do' is default
```
and then in your application
```
$builder = $requestBuilderFactory->create();
$builder->setResponseUrl('http://example.com/order.php');
$builder->setOrderInfo(100001 /* webpay order number */, 12345678 /* eshop order number */);
$builder->setPayment(10.50); // optionally Request::CZK, Request::EUR, Request::USD as second parameter, CZK is default

/* validates all required fields are provided */
$request = $builder->build();

$template->url = $request->getRequestUrl();
```

## Accepting response

```
  use JedenWeb\Webpay;

  $response = new Webpay\Response('public.pem');
  $response->setResponseParams($httpRequest->getQuery()); // $_GET is ugly

  if ($response->verify(/* optionally merchant number */)) // authentic a successful
    ...
```

In Nette
```
// throws JedenWeb\Webpay\InvalidStateException if a required field is missing in query
$response = $responseFactory->create();

if ($response->verify(/* optionally merchant number */)) // authentic a successful
	...
```

