<?php

namespace JustSomeCode\U2F\Actions\DecodeRegistrationResponse;

use JustSomeCode\U2F\DTO\RegistrationResponse;

class DecodeRegistrationResponseState
{
    protected string $decodedRegistrationData = '';
    protected array $unpackedRegistration = [];
    protected string $decodedClientData = '';
    protected array $unpackedClientData = [];
    protected int $pkiParsingOffset = 1;
    protected string $publicKey = '';
    protected string $keyHandle = '';
    protected string $rawCert = '';
    protected string $pemCert = '';
    protected string $extractedSignature;
    protected string $dataToVerify;

    public function __construct(
        public readonly RegistrationResponse $registrationResponse
    ){}

    public function setDecodedRegistrationData(string $decodedRegistrationData): void
    {
        $this->decodedRegistrationData = $decodedRegistrationData;
    }

    public function getDecodedRegistrationData(): string
    {
        return $this->decodedRegistrationData;
    }

    public function setUnpackedRegistration(array $unpackedRegistration): void
    {
        $this->unpackedRegistration = $unpackedRegistration;
    }

    public function getUnpackedRegistration(): array
    {
        return $this->unpackedRegistration;
    }

    public function setDecodedClientData(string $decodedClientData): void
    {
        $this->decodedClientData = $decodedClientData;
    }

    public function getDecodedClientData(): string
    {
        return $this->decodedClientData;
    }

    public function setUnpackedClientData(array $unpackedClient): void
    {
        $this->unpackedClientData = $unpackedClient;
    }

    public function getUnpackedClientData(): array
    {
        return $this->unpackedClientData;
    }

    public function getPKIParsingOffset(): int
    {
        return $this->pkiParsingOffset;
    }

    public function incrementPKIParsingOffset(int $value): int
    {
        return $this->pkiParsingOffset += $value;
    }

    public function setPublicKey(string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setKeyHandle(string $keyHandle): void
    {
        $this->keyHandle = $keyHandle;
    }

    public function getKeyHandle(): string
    {
        return $this->keyHandle;
    }

    public function setRawCert(string $rawCert): void
    {
        $this->rawCert = $rawCert;
    }

    public function getRawCert(): string
    {
        return $this->rawCert;
    }

    public function setPemCert(string $pemCert): void
    {
        $this->pemCert = $pemCert;
    }

    public function getPemCert(): string
    {
        return $this->pemCert;
    }

    public function setExtractedSignature(string $extractedSignature): void
    {
        $this->extractedSignature = $extractedSignature;
    }

    public function getExtractedSignature(): string
    {
        return $this->extractedSignature;
    }

    public function setDataToVerify(string $dataToVerify): void
    {
        $this->dataToVerify = $dataToVerify;
    }

    public function getDataToVerify(): string
    {
        return $this->dataToVerify;
    }
}