# 12andus Client

This is an API client for the 12andus website.

To use this library, please contact 12andus.com support for details.

## Usage

To install:

```
composer require mapkyca/12andus-connect
```


Then create a client library with your public and secret keys:


```
use TwelveAndUs\API\Connect;

$client = new Client($publickey, $secretkey);

```

## Tests

Place your public keys and secret keys in your environment, then run `phpunit`

```
export PUBKEY=xxxxxx
export SECKEY=yyyyyy

phpunit
```