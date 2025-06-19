<?php

namespace App;

use App\Message\GenerateCompensationReport;
use App\Message\GenerateIncidentReport;
use App\Message\GenerateIncidents;
use App\Message\GenerateProductionReport;
use App\Message\GenerateProducts;
use App\Trigger\ExcludeBreaksTrigger;
use App\Trigger\ExcludeDownTimeTrigger;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule as SymfonySchedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule]
class Schedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {}

    public function getSchedule(): SymfonySchedule {
        return new SymfonySchedule()
            ->stateful($this->cache) // ensure missed tasks are executed
            ->processOnlyLastMissedRun(true) // ensure only last missed task is run
            ->add(
                RecurringMessage::every(
                    'last day of this month',
                    new GenerateCompensationReport()
                ),
                RecurringMessage::trigger(
                    new ExcludeDownTimeTrigger(
                        CronExpressionTrigger::fromSpec('0 */6 * * *')
                    ),
                    new GenerateIncidentReport()
                ),
                RecurringMessage::trigger(
                    new ExcludeDownTimeTrigger(
                        CronExpressionTrigger::fromSpec('@midnight', 'Production report generation context')
                    ),
                    new GenerateProductionReport()
                ),
                RecurringMessage::trigger(
                    new ExcludeDownTimeTrigger(
                        new ExcludeBreaksTrigger(
                            CronExpressionTrigger::fromSpec('#hourly', 'Product Generation Context')
                        )
                    ),
                    new GenerateProducts()
                ),
                RecurringMessage::trigger(
                    new ExcludeDownTimeTrigger(
                        new ExcludeBreaksTrigger(
                            CronExpressionTrigger::fromSpec('#hourly', 'Incident Generation Context')
                        )
                    ),
                    new GenerateIncidents()
                ),
            );
    }
}
