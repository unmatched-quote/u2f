<?php

namespace JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\Stages;

use JustSomeCode\U2F\Exceptions\ReplayException;
use JustSomeCode\U2F\Actions\ProcessAuthenticationResponse\ProcessAuthenticationResponseState;

class VerifyCounterAgainstReplay
{
    public function handle(ProcessAuthenticationResponseState $state): ProcessAuthenticationResponseState
    {
        // Logic here is very simple: counter value in persistent storage (i.e. KSM, hardware KSM or DB)
        // must be **LESS** than what's in response.
        // To ensure concurrency-related problems, a trigger for updating is needed that does not allow updates
        // of counter values LESS than current one in db, i.e. counter update must behave like auto_increment,
        // (sequentially) larger numbers

        $result = $state->key->counter < $state->getCounterValue();

        if(!$result)
        {
            throw new ReplayException('Replay attempt detected. Counter value does not pass the check.');
        }

        $state->setCounterCheck($result);

        return $state;
    }
}