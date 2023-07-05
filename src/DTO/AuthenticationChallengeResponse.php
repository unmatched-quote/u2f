<?php

namespace JustSomeCode\U2F\DTO;

final readonly class AuthenticationChallengeResponse
{
    public function __construct(
        public string $clientData,
        public string $keyHandle,
        public string $signatureData
    ){}
}