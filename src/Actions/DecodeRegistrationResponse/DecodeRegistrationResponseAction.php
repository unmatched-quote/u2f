<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse;

use JustSomeCode\U2F\{
    Actions\DecodeRegistrationResponse\Stages\CreateDataToVerify,
    Actions\DecodeRegistrationResponse\Stages\ExtractKeyHandle,
    Actions\DecodeRegistrationResponse\Stages\ExtractSignature,
    Actions\DecodeRegistrationResponse\Stages\VerifySignature,
    DTO\DecodedRegistrationResponse,
    Pipeline,
    DTO\RegistrationResponse,
    Actions\DecodeRegistrationResponse\Stages\DecodeClientData,
    Actions\DecodeRegistrationResponse\Stages\UnpackClientData,
    Actions\DecodeRegistrationResponse\Stages\ExtractPublicKey,
    Actions\DecodeRegistrationResponse\Stages\ExtractCertificate,
    Actions\DecodeRegistrationResponse\Stages\DecodeRegistrationData,
    Actions\DecodeRegistrationResponse\Stages\UnpackRegistrationData
};

use function JustSomeCode\U2F\u2f_str_encode;

class DecodeRegistrationResponseAction
{
    protected DecodedRegistrationResponse $result;

    public function execute(RegistrationResponse $response): self
    {
        $state = new DecodeRegistrationResponseState($response);

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
            ->then(function(DecodeRegistrationResponseState $state)
            {
                return new DecodedRegistrationResponse(
                    base64_encode($state->getPublicKey()),
                    u2f_str_encode($state->getKeyHandle()),
                    base64_encode($state->getRawCert()),
                    $state->registrationResponse->appId
                );
            });

        return $this;
    }

    public function getResult(): DecodedRegistrationResponse
    {
        if(!$this->result instanceof DecodedRegistrationResponse)
        {
            throw new \BadMethodCallException('Method getResult() called before execute(). Result is unavailable, run execute() method first.');
        }

        return $this->result;
    }
}