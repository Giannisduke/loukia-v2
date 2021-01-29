<?php

/**
 * Price none adjustment.
 *
 * @package WPDesk\WooCommerceShipping\HandlingFees
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\HandlingFees;

/**
 * Can apply none value to price.
 */
class PriceAdjustmentNone implements \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustment
{
    const ADJUSTMENT_TYPE = 'none';
    /**
     * @param float $price
     *
     * @return float
     */
    public function apply_on_price($price)
    {
        return $price;
    }
}
