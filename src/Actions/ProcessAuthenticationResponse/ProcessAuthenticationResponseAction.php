<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse;

use JustSomeCode\U2F\Pipeline;
use JustSomeCode\U2F\KSM\U2FKey;
use JustSomeCode\U2F\DTO\AuthenticationResult;
use JustSomeCode\U2F\DTO\AuthenticationChallengeResponse;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\VerifyChallenge;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\VerifyKeyHandle;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\DecodeClientData;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\DecodeSignatureData;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\VerifySignatureHash;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\ExtractCounterFromResponse;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\VerifyCounterAgainstReplay;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages\ExtractChallengeFromResponse;

class ProcessAuthenticationResponseAction
{
    protected AuthenticationResult $result;

    public function execute(U2FKey $key, AuthenticationChallengeResponse $response): self
    {
        $state = new ProcessAuthenticationResponseState($key, $response);

        $pipeline = new Pipeline;

        $this->result = $pipeline
            ->send($state)
            ->through([
                DecodeClientData::class,
                DecodeSignatureData::class,
                ExtractChallengeFromResponse::class,
                ExtractCounterFromResponse::class,
                VerifySignatureHash::class,
                VerifyKeyHandle::class,
                VerifyChallenge::class,
                VerifyCounterAgainstReplay::class,
            ])
            ->then(function(ProcessAuthenticationResponseState $state)
            {
                return new AuthenticationResult(
                    $state->key->keyHandle,
                    $state->getCounterValue(),
                    $state->getChallengeInResponse(),
                    $state->key->appId
                );
            });

        return $this;
    }

    public function getResult(): AuthenticationResult
    {
        if(empty($this->result))
        {
            throw new \BadMethodCallException('Method getResult() called before execute(). Result is unavailable, run execute() method first.');
        }

        return $this->result;
    }
}