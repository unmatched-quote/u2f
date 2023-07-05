<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse;

use JustSomeCode\U2F\{
    Pipeline,
    DTO\RegistrationResponse,
    DTO\DecodedRegistrationResponse,
    Actions\ProcessRegistrationResponse\Stages\CreateDataToVerify,
    Actions\ProcessRegistrationResponse\Stages\ExtractKeyHandle,
    Actions\ProcessRegistrationResponse\Stages\ExtractSignature,
    Actions\ProcessRegistrationResponse\Stages\VerifySignature,
    Actions\ProcessRegistrationResponse\Stages\DecodeClientData,
    Actions\ProcessRegistrationResponse\Stages\UnpackClientData,
    Actions\ProcessRegistrationResponse\Stages\ExtractPublicKey,
    Actions\ProcessRegistrationResponse\Stages\ExtractCertificate,
    Actions\ProcessRegistrationResponse\Stages\DecodeRegistrationData,
    Actions\ProcessRegistrationResponse\Stages\UnpackRegistrationData
};

class ProcessRegistrationResponseAction
{
    protected DecodedRegistrationResponse $result;

    public function execute(RegistrationResponse $response): self
    {
        $state = new ProcessRegistrationResponseState($response);

        $pipeline = new Pipeline;

        $this->result = $pipeline
            ->send($state)
            ->through([
                // Read the string with registration data provided via input and clean the input for further processing
                DecodeRegistrationData::class,
                // Unpack registration data using unpack, format is unsigned char
                // "C*" where * is repeater until the end of data
                UnpackRegistrationData::class,
                // Read the string with client data and clean the input for further processing
                DecodeClientData::class,
                // Interpret the string into PHP data structure via json_decode
                UnpackClientData::class,
                // Extract public key supplied by the hardware device
                ExtractPublicKey::class,
                // Extract key handle
                ExtractKeyHandle::class,
                // Extract the certificate (raw and parsed)
                ExtractCertificate::class,
                // Extract the signature, used to verify the key
                ExtractSignature::class,
                // Compute the data to verify
                CreateDataToVerify::class,
                // Verify signature
                VerifySignature::class,
            ])
            ->then(function(ProcessRegistrationResponseState $state)
            {
                return new DecodedRegistrationResponse(
                    $state->getPublicKey(),
                    $state->getKeyHandle(),
                    $state->getRawCert(),
                    $state->registrationResponse->appId
                );
            });

        return $this;
    }

    public function getResult(): DecodedRegistrationResponse
    {
        if(empty($this->result))
        {
            throw new \BadMethodCallException('Method getResult() called before execute(). Result is unavailable, run execute() method first.');
        }

        return $this->result;
    }
}