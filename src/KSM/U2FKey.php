<?php

namespace JustSomeCode\U2F\KSM;

use JustSomeCode\U2F\Protocol\Constants;

class U2FKey
{
    public function __construct(
        public readonly string $appId,
        public readonly string $keyHandle,
        protected int $counter = 0,
        public readonly ?string $version = Constants::U2F_VERSION,
        protected ?string $challenge = null,
        protected ?string $publicKey = null
    ){}

    public function __serialize(): array
    {
        return [
            'appId' => $this->appId,
            'keyHandle' => $this->keyHandle,
            'version' => $this->version,
            'challenge' => $this->challenge,
            'counter' => $this->counter
        ];
    }
}