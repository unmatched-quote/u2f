<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\ProcessAuthenticationResponseState;

class VerifyKeyHandle
{
    public function handle(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        // Verify that the response contains key handle for the target key we have in KSM
        $result = 0 === strcmp($state->key->keyHandle, $state->response->keyHandle);

        if(!$result)
        {
            throw new \UnexpectedValueException(
                sprintf('Key handle mismatch. Given key does not correspond to request. Key handle: %s. Request key handle: %s',
                    $state->key->keyHandle,
                    $state->response->keyHandle
                )
            );
        }

        $state->setKeyHandleVerified(true);

        return $state;
    }
}