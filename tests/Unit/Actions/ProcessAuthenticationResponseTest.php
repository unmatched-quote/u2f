<?php

namespace JustSomeCode\Tests\Unit\Actions;

use PHPUnit\Framework\TestCase;
use JustSomeCode\U2F\KSM\U2FKey;
use JustSomeCode\U2F\DTO\AuthenticationChallengeResponse;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\VerifyKeyHandle;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\VerifyChallenge;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\DecodeClientData;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\DecodeSignatureData;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\VerifySignatureHash;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\ExtractCounterFromResponse;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\VerifyCounterAgainstReplay;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\ProcessAuthenticationResponseState;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\ExtractChallengeFromResponse;
use function JustSomeCode\U2F\u2f_pub2pem;
use function JustSomeCode\U2F\u2f_str_decode;

class ProcessAuthenticationResponseTest extends TestCase
{
    public function testStateObjectCreatedOk(): ProcessAuthenticationResponseState
    {
        $key = $this->provideU2FKeyDTO();
        $response = $this->provideAuthenticationResponseDTO();

        $state = new ProcessAuthenticationResponseState($key, $response);

        $this->assertInstanceOf(ProcessAuthenticationResponseState::class, $state);
        $this->assertInstanceOf(U2FKey::class, $state->key);
        $this->assertInstanceOf(AuthenticationChallengeResponse::class, $state->response);

        return $state;
    }

    /**
     * @depends testStateObjectCreatedOk
     */
    public function testDecodeClientData(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
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
    public function testDecodeSignatureData(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $stage = new DecodeSignatureData();

        $state = $stage->handle($state);

        $this->assertIsString($state->getDecodedSignatureData());
        $this->assertTrue(strlen($state->getDecodedSignatureData()) > 0);

        return $state;
    }

    /**
     * @depends testDecodeSignatureData
     */
    public function testExtractChallengeFromResponse(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $stage = new ExtractChallengeFromResponse();

        $state = $stage->handle($state);

        $this->assertIsString($state->getChallengeInResponse());
        $this->assertEquals('YnJHbzRiNXdQYW5YNnBhbUozOHBJaVpkZ0Jrb3pzSVU', $state->getChallengeInResponse());

        return $state;
    }

    /**
     * @depends testExtractChallengeFromResponse
     */
    public function testExtractCounterFromResponse(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $stage = new ExtractCounterFromResponse();

        $state = $stage->handle($state);

        $this->assertIsInt($state->getCounterValue());
        $this->assertEquals(27, $state->getCounterValue());

        return $state;
    }

    /**
     * @depends testDecodeSignatureData
     */
    public function testVerifySignatureHash(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $stage = new VerifySignatureHash();

        $state = $stage->handle($state);

        $this->assertTrue($state->getSignatureVerified());

        return $state;
    }

    /**
     * @depends testVerifySignatureHash
     */
    public function testVerifyKeyHandle(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $stage = new VerifyKeyHandle();

        $state = $stage->handle($state);

        $this->assertTrue($state->getKeyHandleVerified());

        return $state;
    }

    /**
     * @depends testVerifyKeyHandle
     */
    public function testVerifyChallenge(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $stage = new VerifyChallenge();

        $state = $stage->handle($state);

        $this->assertTrue($state->getChallengeVerified());

        return $state;
    }

    /**
     * @depends testVerifyChallenge
     */
    public function testVerifyCounterAgainstReplay(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        $stage = new VerifyCounterAgainstReplay();

        $state = $stage->handle($state);

        $this->assertTrue($state->getCounterCheck());

        return $state;
    }

    protected function provideU2FKeyDTO(): U2FKey
    {
        return new U2FKey(...$this->provideU2FKeyData());
    }

    protected function provideU2FKeyData(): array
    {
        $key = require(__DIR__ . '/../../Data/valid_key_storage_module_data.php');

        // Convert key
        $key['publicKey'] = u2f_pub2pem(u2f_str_decode($key['publicKey']));

        return $key;
    }

    protected function provideAuthenticationResponseDTO(): AuthenticationChallengeResponse
    {
        return new AuthenticationChallengeResponse(...$this->provideAuthenticationResponseData());
    }

    protected function provideAuthenticationResponseData(): array
    {
        // responseData
        $data = require(__DIR__ . '/../../Data/valid_authentication_signing_response.php');

        return $data['responseData'];
    }
}