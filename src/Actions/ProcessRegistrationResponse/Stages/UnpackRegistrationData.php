<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\DecodeRegistrationResponseState;

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