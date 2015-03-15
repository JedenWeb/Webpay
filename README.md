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
  $request->setPayment(10.50); // optionally 'CZK', 'EUR', 'USD' as second parameter, CZK is default
  echo "<a href='{$request->getRequestUrl()}'>Pay</a>";
```

## Accepting response

```
  use JedenWeb\Webpay;

  $response = new Webpay\Response('public.pem');
  $response->setResponseParams($httpRequest->getQuery()); // $_GET is ugly

  if ($response->verify(/* optionally merchant number */)) // authentic a successful
    ...
```

