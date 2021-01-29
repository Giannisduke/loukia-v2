<?php

/**
 * Simple DTO: Money class.
 *
 * @package WPDesk\AbstractShipping
 */
namespace DhlVendor\WPDesk\AbstractShipping\Rate;

/**
 * Class for prepare data returned by plugin.
 *
 * @package WPDesk\AbstractShipping
 */
final class Money
{
    /**
     * Value in string field but usually is INT
     *
     * @var string|float
     */
    public $amount;
    /**
     * Currency.
     *
     * @var string In ISO 4217
     */
    public $currency;
}
