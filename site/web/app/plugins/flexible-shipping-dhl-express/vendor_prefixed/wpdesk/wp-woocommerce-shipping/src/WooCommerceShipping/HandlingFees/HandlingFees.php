<?php

/**
 * Handling fees.
 *
 * @package WPDesk\WooCommerceShipping\HandlingFees
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\HandlingFees;

/**
 * Can apply handling fees to price.
 */
class HandlingFees
{
    /**
     * Adjustment value.
     *
     * @var float
     */
    private $adjustment_value;
    /**
     * Rounding
     *
     * @var float
     */
    private $rounding;
    /**
     * @var PriceAdjustment
     */
    private $price_adjustment;
    /**
     * HandlingFees constructor.
     *
     * @param PriceAdjustment $price_adjustment Price adjustment.
     * @param float           $adjustment_value Adjustment value.
     * @param float           $rounding Rounding.
     */
    public function __construct($price_adjustment, $adjustment_value, $rounding)
    {
        $this->adjustment_value = $adjustment_value;
        $this->rounding = $rounding;
        $this->price_adjustment = $price_adjustment;
    }
    /**
     * Apply fees to price.
     *
     * @param float $price Input price.
     *
     * @return float
     */
    public function apply_fees_to_price($price)
    {
        return $this->price_adjustment->apply_on_price($price);
    }
}
