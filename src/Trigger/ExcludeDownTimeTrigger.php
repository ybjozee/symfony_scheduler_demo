<?php

namespace App\Trigger;

use App\Helper\FactoryOperationsHelper;
use DateTimeImmutable;
use Symfony\Component\Scheduler\Trigger\TriggerInterface;

class ExcludeDownTimeTrigger implements TriggerInterface {

    public function __construct(private TriggerInterface $inner) { }

    /**
     * @inheritDoc
     */
    public function __toString()
    : string {

        return $this->inner.' (except down time)';
    }

    /**
     * @inheritDoc
     */
    public function getNextRunDate(DateTimeImmutable $run)
    : ?DateTimeImmutable {

        if (!$nextRun = $this->inner->getNextRunDate($run)) {
            return null;
        }

        while (FactoryOperationsHelper::isDownTime($nextRun)) {
            $nextRun = $this->inner->getNextRunDate($nextRun);
        }

        return $nextRun;
    }
}
