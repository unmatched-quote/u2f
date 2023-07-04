<?php

namespace JustSomeCode\U2F\DTO;

final readonly class RegistrationChallenge
{
    public function __construct(
        public string $challenge,
        public string $version,
        public string $appId
    ){}

    public function __serialize(): array
    {
        return [
            'challenge' => $this->challenge,
            'version' => $this->version,
            'appId' => $this->appId
        ];
    }
}