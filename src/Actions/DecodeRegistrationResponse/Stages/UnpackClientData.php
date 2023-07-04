<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseState;

class UnpackClientData
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        // throw \JsonException if invalid string provided
        $decoded = json_decode($state->getDecodedClientData(), true, 512, JSON_THROW_ON_ERROR);

        $state->setUnpackedClientData(
            $decoded
        );

        return $state;
    }
}