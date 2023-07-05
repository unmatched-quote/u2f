<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\ProcessAuthenticationResponseState;

class VerifyChallenge
{
    public function handle(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        // Verify that the initiator is the key that signed the request @ frontend
        $result = 0 === strcmp($state->key->challenge, $state->getChallengeInResponse());

        if(!$result)
        {
            throw new \UnexpectedValueException(
                sprintf('Challenge mismatch. Expected: %s. Received: %s',
                    $state->key->challenge,
                    $state->getChallengeInResponse()
                )
            );
        }

        $state->setChallengeVerified(true);

        return $state;
    }
}