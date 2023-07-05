<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\DecodeRegistrationResponseState;
use function JustSomeCode\U2F\u2f_str_decode;

class DecodeRegistrationData
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $state->setDecodedRegistrationData(
            u2f_str_decode($state->registrationResponse->registrationData)
        );

        return $state;
    }
}