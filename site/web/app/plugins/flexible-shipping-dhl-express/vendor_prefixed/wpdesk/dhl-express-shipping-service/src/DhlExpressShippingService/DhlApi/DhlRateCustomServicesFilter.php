<?php

namespace DhlVendor\WPDesk\DhlExpressShippingService\DhlApi;

use DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use DhlVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlServices;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition;
/**
 * Can filter rates using custom services settings.
 *
 * @package WPDesk\DhlExpressShippingService\DhlApi
 */
class DhlRateCustomServicesFilter implements \DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating
{
    /** @var ShipmentRating */
    private $rating;
    /** @var SettingsValues */
    private $settings;
    public function __construct(\DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating $rating, \DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings)
    {
        $this->rating = $rating;
        $this->settings = $settings;
    }
    /**
     * Filter rates to custom services.
     *
     * @param $ratings
     *
     * @return SingleRate[]
     */
    private function filter_custom_services($ratings)
    {
        $rates = [];
        $services_settings = $this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_SERVICES_TABLE, array());
        if (!\is_array($services_settings)) {
            $services_settings = array();
        }
        $dhl_services = new \DhlVendor\WPDesk\DhlExpressShippingService\DhlServices();
        $enabled_services = $dhl_services->get_enabled_services_from_settings($services_settings);
        foreach ($ratings as $single_rate) {
            if (isset($single_rate->service_type) && isset($enabled_services[$single_rate->service_type])) {
                $single_rate->service_name = $enabled_services[$single_rate->service_type];
                $rates[$single_rate->service_type] = $single_rate;
            }
        }
        return $this->sort_services($rates, $enabled_services);
    }
    /**
     * Get filtered ratings.
     *
     * @return SingleRate[]
     */
    public function get_ratings()
    {
        $rates = [];
        $ratings = $this->rating->get_ratings();
        if (!empty($ratings)) {
            if ($this->is_custom_services_enable($this->settings)) {
                $rates = $this->filter_custom_services($ratings);
            } else {
                $possible_services = \DhlVendor\WPDesk\DhlExpressShippingService\DhlServices::SERVICES;
                foreach ($ratings as $single_rate) {
                    if (isset($single_rate->service_type) && isset($possible_services[$single_rate->service_type])) {
                        $single_rate->service_name = \DhlVendor\WPDesk\DhlExpressShippingService\DhlServices::SERVICES[$single_rate->service_type];
                        $rates[$single_rate->service_type] = $single_rate;
                    }
                }
            }
        }
        return $rates;
    }
    /**
     * Sort rates according to order set in admin settings.
     *
     * @param SingleRate[] $rates Rates.
     * @param array $option_services Saved services to settings.
     *
     * @return SingleRate[]
     */
    private function sort_services($rates, $option_services)
    {
        if (!empty($option_services)) {
            $services = [];
            foreach ($option_services as $service_code => $service_name) {
                if (isset($rates[$service_code])) {
                    $services[] = $rates[$service_code];
                }
            }
            return $services;
        }
        return $rates;
    }
    /**
     * Are customs service settings enabled.
     *
     * @param SettingsValues $settings Values.
     *
     * @return bool
     */
    private function is_custom_services_enable(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings)
    {
        return $settings->has_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_ENABLE_CUSTOM_SERVICES) && 'yes' === $settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_ENABLE_CUSTOM_SERVICES);
    }
}
