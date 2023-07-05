<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\ProcessAuthenticationResponseState;
use function JustSomeCode\U2F\u2f_str_decode;

class DecodeClientData
{
    public function handle(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $decoded = u2f_str_decode($state->response->clientData);

        $state->setDecodedClientData($decoded);

        return $state;
    }
}