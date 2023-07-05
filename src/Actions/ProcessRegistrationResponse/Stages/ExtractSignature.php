<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\DecodeRegistrationResponseState;

class ExtractSignature
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $signature = substr($state->getDecodedRegistrationData(), $state->getPKIParsingOffset());

        $state->setExtractedSignature($signature);

        return $state;
    }
}