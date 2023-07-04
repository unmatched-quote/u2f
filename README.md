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