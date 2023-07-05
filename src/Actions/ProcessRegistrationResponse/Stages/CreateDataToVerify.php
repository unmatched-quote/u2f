<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\ProcessRegistrationResponseState;

class CreateDataToVerify
{
    public function handle(ProcessRegistrationResponseState $state): ProcessRegistrationResponseState
    {
        $data = chr(0);
        $data .= hash('sha256', $state->registrationResponse->appId, true);
        $data .= hash('sha256', $state->getDecodedClientData(), true);
        $data .= $state->getKeyHandle();
        $data .= $state->getRawPublicKey();

        $state->setDataToVerify($data);

        return $state;
    }
}