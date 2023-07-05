<?php

namespace JustSomeCode\U2F\DTO;

final readonly class AuthenticationResult
{
    public function __construct(
        public string $keyHandle,
        public int $counter,
        public string $challenge,
        public string $appId
    ){}
}