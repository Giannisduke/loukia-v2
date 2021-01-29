<?php

/**
 * Capability: CanRate class
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability;

use DhlVendor\WPDesk\AbstractShipping\CollectionPoints\CollectionPoint;
use DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment;
/**
 * Interface for rate shipment
 *
 * @package WPDesk\AbstractShipping\ShippingServiceCapability
 */
interface CanRate
{
    /**
     * Rate shipment.
     *
     * @param SettingsValues  $settings Settings.
     * @param Shipment        $shipment Shipment.
     *
     * @return ShipmentRating
     */
    public function rate_shipment(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment);
    /**
     * Is rate enabled?
     *
     * @param SettingsValues $settings .
     *
     * @return bool
     */
    public function is_rate_enabled(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings);
}
