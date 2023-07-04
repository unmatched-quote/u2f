<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseState;
use function JustSomeCode\U2F\u2f_str_decode;

class DecodeClientData
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $state->setDecodedClientData(
            u2f_str_decode($state->registrationResponse->clientData)
        );

        return $state;
    }
}