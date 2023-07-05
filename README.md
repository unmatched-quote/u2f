# PHP U2F parser library

## Requirements

 - PHP 8.2+
 - OpenSSL extension

 ## Installation

 ```bash
 composer require just-some-code/u2f
 ```

 ## Description

 U2F is a request-response type protocol. It means there's a challenge function and you need to
 parse the result of challenge processing (response) using an appropriate function, depending on whether you're
 performing a registration (enrollment) or authentication (signing).2

 tl;dr:

 1. generate a challenge
 2. your browser passes it to hardware U2F device
 3. do magic using u2f.js
 4. send the response to serverside and parse it

 This repo deals with steps 1 and 4.

## Testing

1. clone the repository
2. `composer install`
3. `./vendor/bin/phpunit`

Or check this repository's test results.

## Use

### Registration (enrollment)

#### Generate a challenge

```php
use function JustSomeCode\u2f_enroll_challenge;

// App ID must be the scheme + domain name of the origin that initiates U2F
// Translated to normal language, the URL displayed in your browser where you loaded the UI for dealing with U2F
// is what value of appId must be.
$appId = 'https://frontend.application.com';

echo json_encode(u2f_enroll_challenge($appId));
```

#### Handle registration response

```php
use function JustSomeCode\u2f_enroll_parse;

// Built-in browser extension for U2F handling produces the following object:
// {appId: string, challenge: string, registrationData: string, clientData: string, version: string}

// This example uses $_POST superglobal, expecting data to be supplied via HTTP
$appId = $_POST['appId'];
$challenge = $_POST['challenge'];
$registrationData = $_POST['registrationData'];
$clientData = $_POST['clientData'];
$version = $_POST['version'];

$response = u2f_enroll_parse($appId, $challenge, $registrationData, $clientData, $version);

echo json_encode((array)$response);
```


### Authentication (signing)

#### Generate a challenge

To generate a challenge, it's expected that you load U2F key information from KSM (key storage module - database).

Frontend process is user entering username, sending data to server. Server reads user info from db, loads associated keys
and for each key associated with the user it then generates the auth (signing) challenge.

This function deals with the challenge issuing *for* the key identified by `keyHandle` parameter.

```php
use function JustSomeCode\u2f_auth_challenge;

// Load data from db, using $_POST['username']
// Obtain keyHandle and appId values
$appId = 'https://frontend.application.com';
$keyHandle = '4xv32Z6BJj479D-jJrOja-SQkWkWBp3BGu8uEAX2pBsXAg-6XFI_fpshtMv33wcFL6qeXSKFZmLQih2JrnajVA';

// This is a single challenge. You can issue an array of challenges and handle it @ frontend, then send
// back the value using the key that signed issued challenge
$challenge = u2f_auth_challenge($appId, $keyHandle);

echo json_encode($challenge);
```

#### Handle authentication response

Authentication response is created by browser after user touches the blinking-LED on the hardware token.

Check unit tests for examples on how the data looks like.

```php

use JustSomeCode\U2F\KSM\U2FKey;
use use JustSomeCode\U2F\DTO\AuthenticationResult;
use JustSomeCode\U2F\DTO\AuthenticationChallengeResponse;
use function JustSomeCode\U2F\u2f_auth_parse;

// This is loaded via database / any other persistent storage that links the request with the key
$keyData = [
   'challenge'     => 'YnJHbzRiNXdQYW5YNnBhbUozOHBJaVpkZ0Jrb3pzSVU',
   'publicKey'     => 'BEfBekIF7AhKwgi4QpQg0mQUQmIIEq6fDCOEv8auCDNNfeNpdNLrRFfAB9KjlBxp/pxjl45v2CnSOuSH+nmHMuk=',
   'keyHandle'     => 'TBl7BtPWkiiB7GEdphPferDvTdnB1wSdjSYtHYg3BqNh19RKSNgv6-hPROB-pqeoq3rlel5NsnFqeLUAhF5Nbg',
   'appId'         => 'https://server.mfa.web',
   'version'       => 'U2F_V2',
   'counter'       => 0
];

// The JS object (represented as PHP array) created by the U2F module in browser
$responseData = [
    'type'          => 'u2f_sign_response',
    'requestId'     => 1,
    'responseData'  => [
        'clientData'    => 'eyJ0eXAiOiJuYXZpZ2F0b3IuaWQuZ2V0QXNzZXJ0aW9uIiwiY2hhbGxlbmdlIjoiWW5KSGJ6UmlOWGRRWVc1WU5uQmhiVW96T0hCSmFWcGtaMEpyYjNwelNWVSIsIm9yaWdpbiI6Imh0dHBzOi8vc2VydmVyLm1mYS53ZWIiLCJjaWRfcHVia2V5IjoiIn0',
        'keyHandle'     => 'TBl7BtPWkiiB7GEdphPferDvTdnB1wSdjSYtHYg3BqNh19RKSNgv6-hPROB-pqeoq3rlel5NsnFqeLUAhF5Nbg',
        'signatureData' => 'AQAAABswRAIgZnfBV7O_O1j3keClYYtobXWFRbropjiYATy_RoVwp8oCIHFvZjRFP5tGDB1qY6vJZ250yAnw8J_ZLVMZ8RN3kOr5'
    ]
];

$key = new U2FKey(...$keyData);
$response = new AuthenticationChallengeResponse(...$responseData['responseData']);

try
{
    $result = u2f_auth_parse($key, $data); // instanceof AuthenticationResult
    // to do after this line: update the data in DB (counter value)
}
catch(\Exception $e)
{
    // Report the error, log it etc.
}
```