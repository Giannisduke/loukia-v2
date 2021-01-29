<?php

/**
 * Simple DTO: Client class.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace DhlVendor\WPDesk\AbstractShipping\Shipment;

/**
 * Class that stores the client data
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
final class Client
{
    /**
     * Address.
     *
     * @var Address
     */
    public $address;
    /**
     * E-mail.
     *
     * @var string
     */
    public $email;
    /**
     * Phone number.
     *
     * @var string|null
     */
    public $phone_number;
    /**
     * Name.
     *
     * @var string
     */
    public $name;
    /**
     * Company name.
     *
     * @var string|null
     */
    public $company_name;
}
