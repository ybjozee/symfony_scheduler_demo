<?php

namespace App\Trigger;

use App\Helper\FactoryOperationsHelper;
use DateTimeImmutable;
use Symfony\Component\Scheduler\Trigger\TriggerInterface;

class ExcludeBreaksTrigger implements TriggerInterface {

    public function __construct(private TriggerInterface $inner) { }

    /**
     * @inheritDoc
     */
    public function __toString()
    : string {

        return $this->inner.' (except breaks)';
    }

    /**
     * @inheritDoc
     */
    public function getNextRunDate(DateTimeImmutable $run)
    : ?DateTimeImmutable {

        if (!$nextRun = $this->inner->getNextRunDate($run)) {
            return null;
        }

        while (FactoryOperationsHelper::isBreakTime($nextRun)) {
            $nextRun = $this->inner->getNextRunDate($nextRun);
        }

        return $nextRun;
    }
}