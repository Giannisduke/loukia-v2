<?php

/**
 * Custom Exception for InvalidSettingsException.
 *
 * @package WPDesk\AbstractShipping\Exception
 */
namespace DhlVendor\WPDesk\AbstractShipping\Exception;

/**
 * Exception thrown by service in case the settings do not pass the validation.
 *
 * @package WPDesk\AbstractShipping\Exception
 */
class InvalidSettingsException extends \RuntimeException implements \DhlVendor\WPDesk\AbstractShipping\Exception\ShippingException
{
}
