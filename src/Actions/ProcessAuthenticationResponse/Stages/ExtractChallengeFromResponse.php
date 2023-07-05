<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\ProcessAuthenticationResponseState;

class ExtractChallengeFromResponse
{
    public function handle(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $value = json_decode($state->getDecodedClientData(), true);

        if(JSON_ERROR_NONE !== json_last_error())
        {
            throw new \UnexpectedValueException('Non-JSON data found in decoded clientData. Error: '. json_last_error());
        }

        $state->setChallengeInResponse($value['challenge'] ?? '');

        return $state;
    }
}