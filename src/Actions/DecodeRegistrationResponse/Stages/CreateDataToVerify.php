<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseState;

class CreateDataToVerify
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $data = chr(0);
        $data .= hash('sha256', $state->registrationResponse->appId, true);
        $data .= hash('sha256', $state->getDecodedClientData(), true);
        $data .= $state->getKeyHandle();
        $data .= $state->getPublicKey();

        $state->setDataToVerify($data);

        return $state;
    }
}