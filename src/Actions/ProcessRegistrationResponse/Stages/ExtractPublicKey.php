<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Protocol\Constants;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\DecodeRegistrationResponseState;
use function JustSomeCode\U2F\u2f_pub2pem;

class ExtractPublicKey
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $publicKeyString = substr(
            $state->getDecodedRegistrationData(),
            $state->getPKIParsingOffset(),
            Constants::U2F_PUBLIC_KEY_LENGTH
        );

        // Offset value is used to traverse the string supplied by U2F hardware token, use case
        // is value extraction which is length-based
        $state->incrementPKIParsingOffset(Constants::U2F_PUBLIC_KEY_LENGTH);

        // Convert the extracted value to PEM value
        $publicKey = u2f_pub2pem($publicKeyString, Constants::U2F_PUBLIC_KEY_LENGTH);

        $state->setPublicKey($publicKey);
        $state->setRawPublicKey($publicKeyString);

        return $state;
    }
}