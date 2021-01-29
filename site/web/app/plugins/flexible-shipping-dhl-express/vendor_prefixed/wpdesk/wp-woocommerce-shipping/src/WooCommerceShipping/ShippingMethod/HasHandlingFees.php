<?php

/**
 * Capability: HasHandlingFees interface.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod;

/**
 * Interface for handling fees.
 */
interface HasHandlingFees
{
    /**
     * Should apply handling fees.
     * This method should be overwritten on child class when needed.
     *
     * @return bool
     */
    public function should_apply_handling_fees();
    /**
     * Apply handling fees on price if enabled.
     *
     * @param float $price Price to apply on.
     *
     * @return float
     */
    public function apply_handling_fees_if_enabled($price);
}
