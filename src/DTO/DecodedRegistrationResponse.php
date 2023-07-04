<?php

namespace JustSomeCode\U2F\DTO;

final readonly class DecodedRegistrationResponse
{
    public function __construct(
        public string $pubKey,
        public string $keyHandle,
        public string $certificate,
        public string $appId
    ){}
}