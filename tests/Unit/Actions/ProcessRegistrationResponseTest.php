<?php

namespace JustSomeCode\U2F\Tests\Unit\Actions;

use PHPUnit\Framework\TestCase;
use JustSomeCode\U2F\DTO\RegistrationResponse;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\VerifySignature;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\DecodeClientData;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\UnpackClientData;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\ExtractKeyHandle;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\ExtractPublicKey;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\ExtractSignature;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\ExtractCertificate;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\CreateDataToVerify;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\DecodeRegistrationData;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\Stages\UnpackRegistrationData;
use JustSomeCode\U2F\Actions\ProcessRegistrationResponse\DecodeRegistrationResponseState;

class ProcessRegistrationResponseTest extends TestCase
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

    /**
     * @depends testCreateDataToVerify
     */
    public function testVerifySignature(DecodeRegistrationResponseState $state): DecodeRegistrationResponseState
    {
        $stage = new VerifySignature();

        $state = $stage->handle($state);

        $this->assertEquals(1, $state->getSignatureVerification());

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
        return require(__DIR__ . '/../../Data/valid_registration_challenge_response_data.php');
    }
}