<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseState;

class ExtractSignature
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $signature = substr($state->getDecodedRegistrationData(), $state->getPKIParsingOffset());

        $state->setExtractedSignature($signature);

        return $state;
    }
}