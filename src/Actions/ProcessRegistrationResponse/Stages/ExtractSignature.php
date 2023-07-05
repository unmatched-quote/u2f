<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\ProcessRegistrationResponseState;

class ExtractSignature
{
    public function handle(ProcessRegistrationResponseState $state): ProcessRegistrationResponseState
    {
        $signature = substr($state->getDecodedRegistrationData(), $state->getPKIParsingOffset());

        $state->setExtractedSignature($signature);

        return $state;
    }
}