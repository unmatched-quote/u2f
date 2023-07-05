<?php

namespace JustSomeCode\U2F\DTO;

final readonly class AuthenticationChallenge
{
    public function __construct(
        public string $appId,
        public string $challenge,
        public string $keyHandle,
        public string $version
    ){}

    public function __serialize(): array
    {
        return [
            'appId' => $this->appId,
            'challenge' => $this->challenge,
            'keyHandle' => $this->keyHandle,
            'version' => $this->version
        ];
    }
}