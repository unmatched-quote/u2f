<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages;

use JustSomeCode\U2F\Exceptions\InvalidSignatureException;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseState;

class VerifySignature
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $signature = $state->getExtractedSignature();
        $data = $state->getDataToVerify();

        $result = openssl_verify($data, $signature, $state->getPemCert(), 'sha256');

        if($result !== 1)
        {
            throw new InvalidSignatureException('Invalid signature. OpenSSL error: '. openssl_error_string());
        }

        return $state;
    }
}