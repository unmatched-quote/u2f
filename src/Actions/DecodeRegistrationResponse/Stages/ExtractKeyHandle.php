<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseState;

class ExtractKeyHandle
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $index = $state->getPKIParsingOffset();

        // Fetch data at given index
        $length = $state->getUnpackedRegistration()[$index] ?? null;

        // Increment the offset
        $state->incrementPKIParsingOffset(1);

        // Abort if empty
        if(empty($length))
        {
            throw new \UnexpectedValueException("Unexpected value for key handle string data.");
        }

        // Info we're trying to extract in this stage
        $keyHandle = substr($state->getDecodedRegistrationData(), $state->getPKIParsingOffset(), $length);

        // Increment the counter for value of $length
        $state->incrementPKIParsingOffset($length);

        // Save keyhandle to state
        $state->setKeyHandle($keyHandle);

        return $state;
    }
}