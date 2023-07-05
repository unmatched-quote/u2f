<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\ProcessRegistrationResponseState;

class UnpackClientData
{
    public function handle(ProcessRegistrationResponseState $state): ProcessRegistrationResponseState
    {
        // throw \JsonException if invalid string provided
        $decoded = json_decode($state->getDecodedClientData(), true, 512, JSON_THROW_ON_ERROR);

        $state->setUnpackedClientData(
            $decoded
        );

        return $state;
    }
}