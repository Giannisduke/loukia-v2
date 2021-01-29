<?php

/**
 * Class BlackoutLeadDays
 *
 * @package WPDesk\AbstractShipping\Settings
 */
namespace DhlVendor\WPDesk\AbstractShipping\Settings;

/**
 * Can calculate date according to blackout days.
 */
class BlackoutLeadDays
{
    /**
     * @var array
     */
    private $blackout_days;
    /**
     * @var int
     */
    private $lead_days;
    /**
     * BlackoutLeadDays constructor.
     *
     * @param array $blackout_days .
     * @param int   $lead_days .
     */
    public function __construct(array $blackout_days, $lead_days)
    {
        $this->blackout_days = $blackout_days;
        $this->lead_days = $lead_days;
    }
    /**
     * @param \DateTime $calculated_date .
     *
     * @return \DateTime
     */
    public function calculate_date(\DateTime $calculated_date)
    {
        $lead_days = $this->lead_days;
        $one_day_date_interval = new \DateInterval('P1D');
        $calculated_date = $this->increase_date_while_in_blackout_days($calculated_date, $one_day_date_interval);
        while ($lead_days) {
            $calculated_date = $calculated_date->add($one_day_date_interval);
            $calculated_date = $this->increase_date_while_in_blackout_days($calculated_date, $one_day_date_interval);
            $lead_days--;
        }
        return $calculated_date;
    }
    /**
     * @param \DateTime     $calculated_date .
     * @param \DateInterval $one_day_date_interval .
     *
     * @return \DateTime
     */
    private function increase_date_while_in_blackout_days(\DateTime $calculated_date, \DateInterval $one_day_date_interval)
    {
        $calculated_date_week_day = $calculated_date->format('N');
        while (\in_array($calculated_date_week_day, $this->blackout_days, \true)) {
            $calculated_date->add($one_day_date_interval);
            $calculated_date_week_day = $calculated_date->format('N');
        }
        return $calculated_date;
    }
}
