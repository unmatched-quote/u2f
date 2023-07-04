<?php

namespace JustSomeCode\U2F\DTO;

final readonly class RegistrationResponse
{
    public function __construct(
        public string $registrationData,
        public string $challenge,
        public string $version,
        public string $appId,
        public string $clientData
    ){}
}