<?php

namespace JustSomeCode\U2F\Tests\Unit\Actions;

use PHPUnit\Framework\TestCase;
use JustSomeCode\U2F\DTO\RegistrationResponse;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\VerifySignature;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\DecodeClientData;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\UnpackClientData;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\ExtractKeyHandle;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\ExtractPublicKey;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\ExtractSignature;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\ExtractCertificate;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\CreateDataToVerify;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\DecodeRegistrationData;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\Stages\UnpackRegistrationData;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseState;

class DecodeRegistrationResponseTest extends TestCase
{
    public function testDecodeRegistrationResponseCreatedOk(): DecodeRegistrationResponseState
    {
        $state = new DecodeRegistrationResponseState(
            $this->provideRegistrationResponseDTO()
        );

        $this->assertInstanceOf(RegistrationResponse::class, $state->registrationResponse);

        return $state;
    }

    /**
     * @depends testDecodeRegistrationResponseCreatedOk
     */
    public function testDecodeRegistrationDataStage(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new DecodeRegistrationData();

        $state = $stage->handle($state);

        $this->assertIsString($state->getDecodedRegistrationData());
        $this->assertTrue(strlen($state->getDecodedRegistrationData()) > 0);

        return $state;
    }

    /**
     * @depends testDecodeRegistrationDataStage
     */
    public function testUnpackRegistrationData(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new UnpackRegistrationData();

        $state = $stage->handle($state);

        $this->assertIsArray($state->getUnpackedRegistration());
        $this->assertTrue(sizeof($state->getUnpackedRegistration()) > 0);

        return $state;
    }

    /**
     * @depends testUnpackRegistrationData
     */
    public function testDecodeClientData(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new DecodeClientData();

        $state = $stage->handle($state);

        $this->assertIsString($state->getDecodedClientData());
        $this->assertTrue(strlen($state->getDecodedClientData()) > 0);

        return $state;
    }

    /**
     * @depends testDecodeClientData
     */
    public function testUnpackClientData(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new UnpackClientData();

        $state = $stage->handle($state);

        $this->assertIsArray($state->getUnpackedClientData());
        $this->assertArrayHasKey('typ', $state->getUnpackedClientData());
        $this->assertArrayHasKey('challenge', $state->getUnpackedClientData());
        $this->assertArrayHasKey('origin', $state->getUnpackedClientData());
        $this->assertArrayHasKey('cid_pubkey', $state->getUnpackedClientData());

        return $state;
    }

    /**
     * @depends testUnpackClientData
     */
    public function testExtractPublicKey(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new ExtractPublicKey();

        $state = $stage->handle($state);

        $this->assertIsString($state->getPublicKey());

        return $state;
    }

    /**
     * @depends testExtractPublicKey
     */
    public function testExtractKeyHandle(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new ExtractKeyHandle();

        $state = $stage->handle($state);

        $this->assertIsString($state->getKeyHandle());
        $this->assertTrue(strlen($state->getKeyHandle()) > 0);

        return $state;
    }

    /**
     * @depends testExtractKeyHandle
     */
    public function testExtractCertificate(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new ExtractCertificate();

        $state = $stage->handle($state);

        $this->assertIsString($state->getPemCert());
        $this->assertTrue(strlen($state->getPemCert()) > 0);
        $this->assertInstanceOf(\OpenSSLAsymmetricKey::class, openssl_pkey_get_public($state->getPemCert()));

        return $state;
    }

    /**
     * @depends testExtractCertificate
     */
    public function testExtractSignature(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new ExtractSignature();

        $state = $stage->handle($state);

        $this->assertIsString($state->getExtractedSignature());
        $this->assertTrue(strlen($state->getExtractedSignature()) > 0);

        return $state;
    }

    /**
     * @depends testExtractSignature
     */
    public function testCreateDataToVerify(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new CreateDataToVerify();

        $state = $stage->handle($state);

        $this->assertIsString($state->getDataToVerify());
        $this->assertTrue(strlen($state->getDataToVerify()) > 0);

        return $state;
    }

    public function testVerifySignature(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new VerifySignature();

        $state = $stage->handle($state);

        return $state;
    }

    protected function provideRegistrationResponseDTO(): RegistrationResponse
    {
        return new RegistrationResponse(
            ...$this->provideRegistrationData()
        );
    }

    protected function provideRegistrationData(): array
    {
        return [
            'registrationData' => 'BQSl1E1Tc924vsXAXCoCWTyTz9vLCbMwGwdnSYkn3KbfJpxVltmTfkwUmcLW_mAmckCWyyJxJ8XosrngElQi3f0ZQOMb99megSY-O_Q_oyazo2vkkJFpFgadwRrvLhAF9qQbFwIPulxSP36bIbTL998HBS-qnl0ihWZi0Iodia52o1QwggIuMIIBGKADAgECAgQKYwv_MAsGCSqGSIb3DQEBCzAuMSwwKgYDVQQDEyNZdWJpY28gVTJGIFJvb3QgQ0EgU2VyaWFsIDQ1NzIwMDYzMTAgFw0xNDA4MDEwMDAwMDBaGA8yMDUwMDkwNDAwMDAwMFowKTEnMCUGA1UEAwweWXViaWNvIFUyRiBFRSBTZXJpYWwgMTc0MjYzMjk1MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEpCNkXbqLI-1s2eXki5Mqz99FZMdXMfHPUgdFEGtJupYPrQ8KpQk97jOQhEWoFrg2fN2FVxLzSeVm5jcA1CbQn6MmMCQwIgYJKwYBBAGCxAoCBBUxLjMuNi4xLjQuMS40MTQ4Mi4xLjIwCwYJKoZIhvcNAQELA4IBAQBlObAyoc_ESNIHrhSbCra0YMqmUBwTOvCaDqSBLwo-QkpcpK71PUfpcEz34sAT1w3jyhSKsUWYMiVNtlFdTZWbao7052tJUVj6p1sX2i2LwIoCY8e4llJbqKLuo0Tn10cpRo_iUNimeFEOq21ZJRqWHYs8MlBSk71uwk-SA1kCEFRAxhlcv8xBktFiRf6V3uWVM05fm7uf_hjkQyv6Wo7JVUL2VDok_rGhr2rpmn35j1mYsJ5yuty4fr6ksOADM9xzyrGELY5HUHRlKkj1EGBxPQqqdUTS2rUoQN6iQ-oXcyCYNGQn779ralXTABh88Le0LnsG0F0f9RcQHzFlUWG0MEUCICPn5mejgZZ7HAc12IsG_ZFHk2euSJs2Ic5FI5PeBijaAiEAr2zv0YnjK14njrwM5HarglOevsffedupMAzcA_z35WI',
            'challenge' => 'RFpVZFhzdjkyZWdxOVY4NUdEZmN1ZFp6RndiQVJCMTg',
            'version' => 'U2F_V2',
            'appId' => 'https://server.mfa.web',
            'clientData' => 'eyJ0eXAiOiJuYXZpZ2F0b3IuaWQuZmluaXNoRW5yb2xsbWVudCIsImNoYWxsZW5nZSI6IlJGcFZaRmh6ZGpreVpXZHhPVlk0TlVkRVptTjFaRnA2Um5kaVFWSkNNVGciLCJvcmlnaW4iOiJodHRwczovL3NlcnZlci5tZmEud2ViIiwiY2lkX3B1YmtleSI6IiJ9'
        ];
    }
}