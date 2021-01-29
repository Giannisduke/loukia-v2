<?php

namespace DhlVendor\WPDesk\WooCommerceShipping\CustomFields;

use Throwable;
/**
 * Exception when factory can;t create a field.
 *
 * @package WPDesk\WooCommerceShipping\CustomFields
 */
class CouldNotFindService extends \RuntimeException
{
    /**
     * CouldNotFindService constructor.
     *
     * @param $service
     * @param Throwable|null $previous
     */
    public function __construct($service, \Throwable $previous = null)
    {
        $message = \esc_html(\sprintf(\__('Not found HTML view for custom field %1$s.', 'flexible-shipping-dhl-express'), $service));
        parent::__construct($message, 0, $previous);
    }
}
