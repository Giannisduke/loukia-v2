<?php

/**
 * Custom Exception for Rates.
 *
 * @package WPDesk\AbstractShipping\Exception
 */
namespace DhlVendor\WPDesk\AbstractShipping\Exception;

/**
 * Exception thrown when rates is empty.
 *
 * @package WPDesk\AbstractShipping\Exception
 */
class RateException extends \RuntimeException implements \DhlVendor\WPDesk\AbstractShipping\Exception\ShippingException
{
    /**
     * Context.
     *
     * @var array
     */
    private $context;
    /**
     * RateException constructor.
     *
     * @param string $message  Message.
     * @param array  $context  Context.
     * @param int    $code     Code.
     * @param null   $previous Previous.
     */
    public function __construct($message = '', $context = [], $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
    /**
     * Get context.
     *
     * @return array
     */
    public function get_context()
    {
        return $this->context;
    }
}
