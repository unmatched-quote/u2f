<?php

namespace JustSomeCode\U2F\Tests\Unit;

use PHPUnit\Framework\TestCase;
use JustSomeCode\U2F\KSM\U2FKey;
use JustSomeCode\U2F\DTO\AuthenticationResult;
use JustSomeCode\U2F\DTO\AuthenticationChallenge;
use JustSomeCode\U2F\DTO\DecodedRegistrationResponse;
use JustSomeCode\U2F\DTO\AuthenticationChallengeResponse;
use function JustSomeCode\U2F\u2f_pub2pem;
use function JustSomeCode\U2F\u2f_str_decode;
use function JustSomeCode\U2F\u2f_auth_parse;
use function JustSomeCode\U2F\u2f_enroll_parse;
use function JustSomeCode\U2F\u2f_auth_challenge;
use function JustSomeCode\U2F\u2f_enroll_challenge;

class U2FFunctionsTest extends TestCase
{
    public function test_u2f_enroll_challenge_function(): void
    {
        $result = u2f_enroll_challenge(...$this->provideValidAppId());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('appId', $result);
        $this->assertArrayHasKey('version', $result);
        $this->assertArrayHasKey('challenge', $result);

        $this->assertEquals('https://www.u2f-test-domain.com', $result['appId']);
        $this->assertEquals('U2F_V2', $result['version']);

        $this->assertIsString($result['challenge']);
        $this->assertTrue(strlen($result['challenge']) === 32);
    }

    public function test_u2f_enroll_parse_function(): void
    {
        $input = $this->provideValidRegistrationChallengeResponseData();

        $result = u2f_enroll_parse(...$input);

        $this->assertInstanceOf(DecodedRegistrationResponse::class, $result);
    }

    public function test_u2f_auth_challenge_function(): void
    {
        $result = u2f_auth_challenge(...$this->provideValidU2FKeyData());

        $this->assertInstanceOf(AuthenticationChallenge::class, $result);
        $this->assertIsArray($data = (array)$result);
        $this->assertArrayHasKey('appId', $data);
        $this->assertArrayHasKey('challenge', $data);
        $this->assertArrayHasKey('keyHandle', $data);
        $this->assertArrayHasKey('version', $data);
    }

    public function test_u2f_auth_parse_function(): void
    {
        $key = $this->provideU2FKeyDTO();
        $response = $this->provideAuthenticationResponseDTO();

        $result = u2f_auth_parse($key, $response);

        $this->assertInstanceOf(AuthenticationResult::class, $result);
        $this->assertIsInt($result->counter);
        $this->assertEquals(27, $result->counter);
    }

    protected function provideValidAppId(): array
    {
        return [
            'appId' => 'https://www.u2f-test-domain.com',
            'version' => 'U2F_V2'
        ];
    }

    protected function provideValidRegistrationChallengeResponseData(): array
    {
        return require(__DIR__ . '/../Data/valid_registration_challenge_response_data.php');
    }

    protected function provideValidU2FKeyData(): array
    {
        $data = require(__DIR__ . '/../Data/valid_u2f_key.php');

        if(isset($data['version'])) unset($data['version']);

        return $data;
    }

    protected function provideU2FKeyDTO(): U2FKey
    {
        return new U2FKey(...$this->provideU2FKeyData());
    }

    protected function provideU2FKeyData(): array
    {
        $key = require(__DIR__ . '/../Data/valid_key_storage_module_data.php');

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
        $data = require(__DIR__ . '/../Data/valid_authentication_signing_response.php');

        return $data['responseData'];
    }
}