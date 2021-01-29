<?php

/**
 * Trait with handling fees.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\Traits
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits;

use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees;
use DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustment;
use DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentFixed;
use DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentNone;
use DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentPercentage;
/**
 * Can apply handling fees to ratings.
 */
trait HandlingFeesTrait
{
    /**
     * Should apply handling fees.
     * This method should be overwritten on child class when needed.
     *
     * @return bool
     */
    public function should_apply_handling_fees()
    {
        return \true;
    }
    /**
     * Creates price adjustment handler.
     *
     * @param string $price_adjustment_type .
     * @param float  $price_adjustment_value .
     *
     * @return PriceAdjustment
     * @throws \RuntimeException
     */
    private function create_price_adjustment_handler($price_adjustment_type, $price_adjustment_value)
    {
        if ($price_adjustment_type === \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentNone::ADJUSTMENT_TYPE) {
            return new \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentNone();
        } elseif ($price_adjustment_type === \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentFixed::ADJUSTMENT_TYPE) {
            return new \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentFixed($price_adjustment_value, \wc_get_rounding_precision());
        } elseif ($price_adjustment_type === \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentPercentage::ADJUSTMENT_TYPE) {
            return new \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentPercentage($price_adjustment_value, \wc_get_rounding_precision());
        }
        throw new \RuntimeException('Unknown price adjustment type: ' . $price_adjustment_type);
    }
    /**
     * Apply handling fees on price if present.
     *
     * @param float $price Price to apply on.
     *
     * @return float
     */
    public function apply_handling_fees_if_enabled($price)
    {
        if ($this->should_apply_handling_fees()) {
            $price_adjustment_handler = $this->create_price_adjustment_handler($this->get_option(\DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees::OPTION_PRICE_ADJUSTMENT_TYPE, \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentNone::ADJUSTMENT_TYPE), \floatval($this->get_option(\DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees::OPTION_PRICE_ADJUSTMENT_VALUE, '0')));
            return $price_adjustment_handler->apply_on_price($price);
        }
        return $price;
    }
}
