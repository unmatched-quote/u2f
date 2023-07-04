<?php

namespace JustSomeCode\U2F\DTO;

final readonly class RegistrationResponse
{
    public function __construct(
        public readonly string $registrationData,
        public readonly string $challenge,
        public readonly string $version,
        public readonly string $appId,
        public readonly string $clientData
    ){}
}