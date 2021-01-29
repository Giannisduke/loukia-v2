<?php

/**
 * Price percentage adjustment.
 *
 * @package WPDesk\WooCommerceShipping\HandlingFees
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\HandlingFees;

/**
 * Can apply percentage value to price.
 */
class PriceAdjustmentPercentage implements \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustment
{
    const ADJUSTMENT_TYPE = 'percent';
    /**
     * Adjustment percent.
     *
     * @var float
     */
    private $adjustment_percent;
    /**
     * Rounding
     *
     * @var float
     */
    private $rounding;
    /**
     * PriceAdjustmentPercentage constructor.
     *
     * @param float $adjustment_percent Adjustment percent.
     * @param float $rounding Rounding.
     */
    public function __construct($adjustment_percent, $rounding)
    {
        $this->adjustment_percent = $adjustment_percent;
        $this->rounding = $rounding;
    }
    /**
     * @param float $price
     *
     * @return float
     */
    public function apply_on_price($price)
    {
        return \round($price + $price * $this->adjustment_percent / 100, $this->rounding);
    }
}
