<?php

/**
 * Trait with ShippingService static injection
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\Traits
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use DhlVendor\WPDesk\AbstractShipping\ShippingService;
use DhlVendor\WPDesk\WooCommerceShipping\DisplayNoticeLogger;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod;
/**
 * Facilitates access to ShippingService abstract class with rates.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\Traits
 */
trait ShippingServiceTrait
{
    /**
     * @param ShippingMethod $shipping_method
     *
     * @return ShippingService
     */
    private function get_shipping_service(\DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod $shipping_method)
    {
        return $shipping_method->get_plugin_shipping_decisions()->get_shipping_service();
    }
    /**
     * Initializes and injects logger into service.
     *
     * @param ShippingService $service
     *
     * @return LoggerInterface
     */
    private function inject_logger_into(\DhlVendor\WPDesk\AbstractShipping\ShippingService $service)
    {
        if ($this->can_see_logs()) {
            $logger = new \DhlVendor\WPDesk\WooCommerceShipping\DisplayNoticeLogger($this->get_logger($this), $service->get_name());
        } else {
            $logger = new \Psr\Log\NullLogger();
        }
        $service->setLogger($logger);
        return $logger;
    }
}
