<?php

namespace JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages;

use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\DecodeRegistrationResponseState;
use function JustSomeCode\U2F\u2f_raw2pem_cert;
use function JustSomeCode\U2F\u2f_fix_signature_unused_bits;

class ExtractCertificate
{
    public function handle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $offset = $state->getPKIParsingOffset();

        // Do some magic extraction, this is defined by FIDO. Note: format of packing data is a bit ambiguous,
        // take this at face value and as-is
        $certLen = 4;
        $certLen += ($state->getUnpackedRegistration()[$offset + 2] << 8);
        $certLen += ($state->getUnpackedRegistration()[$offset + 3]);

        $rawCert = u2f_fix_signature_unused_bits(substr($state->getDecodedRegistrationData(), $offset, $certLen));
        $pemCert = u2f_raw2pem_cert($rawCert);

        // Increment the offset
        $state->incrementPKIParsingOffset($certLen);

        // Quickly validate by trying to extract public key from the cert extracted
        if(!openssl_pkey_get_public($pemCert))
        {
            throw new \UnexpectedValueException('Failed extracting public key from given certificate - decoding failed. Error reported: '. openssl_error_string());
        }

        // Save raw and pem cert
        $state->setRawCert($rawCert);
        $state->setPemCert($pemCert);

        return $state;
    }
}