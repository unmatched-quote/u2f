<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages;

use JustSomeCode\U2F\Exceptions\InvalidSignatureException;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\ProcessAuthenticationResponseState;

class VerifySignatureHash
{
    public function handle(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $data = hash('sha256', $state->key->appId, true);
        $data .= substr($state->getDecodedSignatureData(), 0, 5);
        $data .= hash('sha256', $state->getDecodedClientData(), true);

        $signature = substr($state->getDecodedSignatureData(), 5);

        $result = openssl_verify($data, $signature, $state->key->publicKey, 'sha256');

        if(1 !== $result)
        {
            throw new InvalidSignatureException('Invalid signature. Error reported: '. openssl_error_string());
        }

        // Set a bool flag that stage passed, used for unit testing
        $state->setSignatureVerified(true);

        return $state;
    }
}