<?php

namespace DhlVendor\WPDesk\DhlExpressShippingService;

/**
 * DHL Express services.
 */
class DhlServices
{
    /**
     * DHL services.
     */
    const SERVICES = ['H' => 'DHL ECONOMY SELECT', 'W' => 'DHL ECONOMY SELECT', 'E' => 'DHL EXPRESS 9:00', 'K' => 'DHL EXPRESS 9:00', 'M' => 'DHL EXPRESS 10:30', 'L' => 'DHL EXPRESS 10:30', 'T' => 'DHL EXPRESS 12:00', 'Y' => 'DHL EXPRESS 12:00', 'P' => 'DHL EXPRESS WORLDWIDE', 'U' => 'DHL EXPRESS WORLDWIDE', 'D' => 'DHL EXPRESS WORLDWIDE (DOCUMENTS)'];
    const DELIMITER = ', ';
    public function get_grouped_services()
    {
        $grouped_services = array();
        foreach (self::SERVICES as $service_code => $service_name) {
            if (isset($grouped_services[$service_name])) {
                $grouped_services[$service_name] = $grouped_services[$service_name] . self::DELIMITER . $service_code;
            } else {
                $grouped_services[$service_name] = $service_code;
            }
        }
        return \array_flip($grouped_services);
    }
    /**
     * @param array $services_settings
     *
     * @return array
     */
    public function get_enabled_services_from_settings(array $services_settings)
    {
        $enabled_services = array();
        $services = self::SERVICES;
        foreach ($services_settings as $services_codes => $single_service_settings) {
            if (isset($single_service_settings['enabled'])) {
                $services_codes_array = \explode(self::DELIMITER, $services_codes);
                foreach ($services_codes_array as $services_code) {
                    if (isset($services[$services_code])) {
                        $enabled_services[$services_code] = $single_service_settings['name'];
                    }
                }
            }
        }
        return $enabled_services;
    }
}
