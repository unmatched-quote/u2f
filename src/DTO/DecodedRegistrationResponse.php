<?php

namespace JustSomeCode\U2F\DTO;

use Attribute;
use function JustSomeCode\U2F\u2f_str_encode;

final readonly class DecodedRegistrationResponse
{
    public function __construct(
        public string $pubKey,
        public string $keyHandle,
        #[\SensitiveParameter(Attribute::TARGET_PROPERTY)] public string $certificate,
        public string $appId
    ){}

    public function __serialize(): array
    {
        return [
            base64_encode($this->pubKey),
            u2f_str_encode($this->keyHandle),
            base64_encode($this->certificate),
            $this->appId
        ];
    }
}