<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseState;

class UnpackRegistrationData
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $unpacked = unpack('C*', $state->getDecodedRegistrationData());

        $state->setUnpackedRegistration(
            array_values($unpacked)
        );

        return $state;
    }
}