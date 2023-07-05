<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse;

use JustSomeCode\U2F\KSM\U2FKey;
use JustSomeCode\U2F\DTO\AuthenticationChallengeResponse;

class ProcessAuthenticationResponseState
{
    protected string $decodedClientData = '';
    protected string $decodedSignatureData = '';
    protected string $challengeInResponse = '';
    protected bool $signatureVerified = false;
    protected int $counterValue = -1;
    protected bool $keyHandleVerified = false;
    protected bool $challengeVerified = false;
    protected bool $counterCheck = false;

    public function __construct(
        public readonly U2FKey $key,
        public readonly AuthenticationChallengeResponse $response
    ){}

    public function setDecodedClientData(string $decodedClientData): void
    {
        $this->decodedClientData = $decodedClientData;
    }

    public function getDecodedClientData(): string
    {
        return $this->decodedClientData;
    }

    public function setDecodedSignatureData(string $decodedSignatureData): void
    {
        $this->decodedSignatureData = $decodedSignatureData;
    }

    public function getDecodedSignatureData(): string
    {
        return $this->decodedSignatureData;
    }

    public function setSignatureVerified(true $which): void
    {
        $this->signatureVerified = $which;
    }

    public function getSignatureVerified(): bool
    {
        return $this->signatureVerified;
    }

    public function setCounterValue(int $value): void
    {
        $this->counterValue = $value;
    }

    public function getCounterValue(): int
    {
        return $this->counterValue;
    }

    public function setKeyHandleVerified(true $which): void
    {
        $this->keyHandleVerified = $which;
    }

    public function getKeyHandleVerified(): bool
    {
        return $this->keyHandleVerified;
    }

    public function setChallengeVerified(true $which): void
    {
        $this->challengeVerified = $which;
    }

    public function getChallengeVerified(): bool
    {
        return $this->challengeVerified;
    }

    public function setChallengeInResponse(string $challenge): void
    {
        $this->challengeInResponse = $challenge;
    }

    public function getChallengeInResponse(): string
    {
        return $this->challengeInResponse;
    }

    public function setCounterCheck(true $which): void
    {
        $this->counterCheck = $which;
    }

    public function getCounterCheck(): bool
    {
        return $this->counterCheck;
    }
}