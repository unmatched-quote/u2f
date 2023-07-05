<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\ProcessAuthenticationResponseState;

class ExtractCounterFromResponse
{
    public function handle(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $data = $state->getDecodedSignatureData();

        $counter = unpack("Nctr", substr($data, 1, 4));

        $value = $counter['ctr'] ?? null;

        if(empty($value))
        {
            throw new \UnexpectedValueException('Invalid counter value in signature. Expected integer, got: '. gettype($value));
        }

        $state->setCounterValue($value);

        return $state;
    }
}