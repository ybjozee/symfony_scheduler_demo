<?php

namespace App\Scheduler;

use App\Message\GenerateCompensationReport;
use App\Message\GenerateIncidentReport;
use App\Message\GenerateIncidents;
use App\Message\GenerateProductionReport;
use App\Message\GenerateProducts;
use App\Trigger\ExcludeBreaksTrigger;
use App\Trigger\ExcludeDownTimeTrigger;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('tasks')]
final class MainSchedule implements ScheduleProviderInterface {

    private ?Schedule $schedule = null;

    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function getSchedule()
    : Schedule {

        return $this->schedule ??= (new Schedule())->add(
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
        )->stateful($this->cache);
    }
}
