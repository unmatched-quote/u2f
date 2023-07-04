<?php

namespace JustSomeCode\U2F;

function str_random(int $length = 16): string
{
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

function u2f_str_encode(string $string): string
{
    return trim(strtr(base64_encode($string), '+/', '-_'), '=');
}

function u2f_str_decode(string $string): string
{
    return base64_decode(strtr($string, '-_', '+/'));
}

function u2f_pub2pem(string $key, int $len = 65): ?string
{
    // x04 is "End Of Transmission"
    if(strlen($key) != $len || $key[0] !== "\x04")
    {
        return null;
    }

    /*
     * Convert the public key to binary DER format first
     * Using the ECC SubjectPublicKeyInfo OIDs from RFC 5480
     *
     *  SEQUENCE(2 elem)                        30 59
     *   SEQUENCE(2 elem)                       30 13
     *    OID1.2.840.10045.2.1 (id-ecPublicKey) 06 07 2a 86 48 ce 3d 02 01
     *    OID1.2.840.10045.3.1.7 (secp256r1)    06 08 2a 86 48 ce 3d 03 01 07
     *   BIT STRING(520 bit)                    03 42 ..key..
     */
    $der  = "\x30\x59\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01";
    $der .= "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42";
    $der .= "\0" . $key;
    $pem  = "-----BEGIN PUBLIC KEY-----\r\n";
    $pem .= chunk_split(base64_encode($der), 64);
    $pem .= "-----END PUBLIC KEY-----";

    return $pem;
}

function u2f_raw2pem_cert(string $cert): string
{
    $pemCert  = "-----BEGIN CERTIFICATE-----\r\n";
    $pemCert .= chunk_split(base64_encode($cert), 64);
    $pemCert .= "-----END CERTIFICATE-----";

    return $pemCert;
}

function u2f_fix_signature_unused_bits(string $cert): string
{
    $table = [
        '349bca1031f8c82c4ceca38b9cebf1a69df9fb3b94eed99eb3fb9aa3822d26e8',
        'dd574527df608e47ae45fbba75a2afdd5c20fd94a02419381813cd55a2a3398f',
        '1d8764f0f7cd1352df6150045c8f638e517270e8b5dda1c63ade9c2280240cae',
        'd0edc9a91a1677435a953390865d208c55b3183c6759c9b5a7ff494c322558eb',
        '6073c436dcd064a48127ddbf6032ac1a66fd59a0c24434f070d4e564c124c897',
        'ca993121846c464d666096d35f13bf44c1b05af205f9b4a1e00cf6cc10c5e511'
    ];

    if(in_array(hash('sha256', $cert), $table))
    {
        $cert[strlen($cert) - 257] = "\0";
    }

    return $cert;
}