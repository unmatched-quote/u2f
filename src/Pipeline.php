<?php

namespace JustSomeCode\U2F;

class Pipeline
{
    protected object $state;
    protected array $stages = [];
    protected array $performance = [];

    public function send(object $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function through(array $stages): self
    {
        $this->stages = $stages;

        return $this;
    }

    public function then(callable $callable): mixed
    {
        foreach($this->stages as $stage)
        {
            $this->performance(function() use ($stage)
            {
                $pipe = new $stage();

                $this->state = $pipe->handle($this->state);
            }, $stage);
        }

        return $callable($this->state);
    }

    public function getPerformance(): array
    {
        return $this->performance;
    }

    protected function performance(callable $callable, string $identifier): void
    {
        $start = microtime(true);

        $callable();

        $total = microtime(true) - $start;

        $this->performance[] = sprintf("\nTime: [%s] seconds, identifier: [%s]", $total, $identifier);
    }
}
