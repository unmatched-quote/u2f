<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\ProcessRegistrationResponseState;

class UnpackRegistrationData
{
    public function handle(ProcessRegistrationResponseState $state): ProcessRegistrationResponseState
    {
        $unpacked = unpack('C*', $state->getDecodedRegistrationData());

        $state->setUnpackedRegistration(
            array_values($unpacked)
        );

        return $state;
    }
}