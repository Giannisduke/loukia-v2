<?php

namespace DhlVendor\WPDesk\DhlExpressShippingService;

use DhlVendor\DHL\Entity\AM\GetQuoteResponse;
use Psr\Log\LoggerInterface;
use DhlVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanInsure;
use DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanPack;
use DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanTestSettings;
use DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings;
use DhlVendor\WPDesk\DhlExpressShippingService\Exception\CurrencySwitcherException;
use DhlVendor\WPDesk\AbstractShipping\Exception\InvalidSettingsException;
use DhlVendor\WPDesk\AbstractShipping\Exception\RateException;
use DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use DhlVendor\WPDesk\AbstractShipping\ShippingService;
use DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRate;
use DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\HasSettings;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\ConnectionChecker;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlRateCurrencyFilter;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlRateCustomServicesFilter;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlRateReplyInterpretation;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlRateRequestBuilder;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\Sender;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlSender;
/**
 * DHL main shipping class injected into WooCommerce shipping method.
 *
 * @package WPDesk\DhlShippingService
 */
class DhlShippingService extends \DhlVendor\WPDesk\AbstractShipping\ShippingService implements \DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\HasSettings, \DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRate, \DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanInsure, \DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanPack, \DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanTestSettings
{
    /** Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /** Shipping method helper.
     *
     * @var ShopSettings
     */
    private $shop_settings;
    const UNIQUE_ID = 'flexible_shipping_dhl_express';
    /**
     * Sender.
     *
     * @var Sender
     */
    private $sender;
    /**
     * DhlShippingService constructor.
     *
     * @param LoggerInterface $logger Logger.
     * @param ShopSettings $helper Helper.
     */
    public function __construct(\Psr\Log\LoggerInterface $logger, \DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings $helper)
    {
        $this->logger = $logger;
        $this->shop_settings = $helper;
    }
    public function is_rate_enabled(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings)
    {
        return \true;
    }
    /**
     * Set logger.
     *
     * @param LoggerInterface $logger Logger.
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Set sender.
     *
     * @param Sender $sender Sender.
     */
    public function set_sender(\DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\Sender $sender)
    {
        $this->sender = $sender;
    }
    /**
     * Get sender.
     *
     * @return Sender
     */
    public function get_sender()
    {
        return $this->sender;
    }
    /**
     * Create reply interpretation.
     *
     * @param GetQuoteResponse $rate_reply .
     * @param ShopSettings $shop_settings .
     * @param SettingsValues $settings .
     *
     * @return DhlRateReplyInterpretation
     */
    protected function create_reply_interpretation(\DhlVendor\DHL\Entity\AM\GetQuoteResponse $rate_reply, $shop_settings, $settings)
    {
        return new \DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlRateReplyInterpretation($rate_reply, $shop_settings->is_tax_enabled(), $shop_settings->get_default_currency());
    }
    /**
     * Rate shipment.
     *
     * @param SettingsValues $settings Settings Values.
     * @param Shipment $shipment Shipment.
     *
     * @return ShipmentRating
     * @throws InvalidSettingsException InvalidSettingsException.
     * @throws RateException RateException.
     * @throws UnitConversionException Weight exception.
     * @throws \Exception
     */
    public function rate_shipment(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment)
    {
        if (!$this->get_settings_definition()->validate_settings($settings)) {
            throw new \DhlVendor\WPDesk\AbstractShipping\Exception\InvalidSettingsException();
        }
        $this->verify_currency($this->shop_settings->get_default_currency(), $this->shop_settings->get_currency());
        $request_builder = $this->create_rate_request_builder($settings, $shipment, $this->shop_settings);
        $request = $request_builder->build_request();
        $this->set_sender(new \DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlSender($this->logger, $this->is_testing($settings)));
        $response = $this->get_sender()->send($request);
        $reply = $this->create_reply_interpretation($response, $this->shop_settings, $settings);
        return $this->create_filter_rates_by_currency(new \DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlRateCustomServicesFilter($reply, $settings));
    }
    /**
     * Create rate request builder.
     *
     * @param SettingsValues $settings .
     * @param Shipment       $shipment .
     * @param ShopSettings   $shop_settings .
     *
     * @return DhlRateRequestBuilder
     */
    protected function create_rate_request_builder(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment, \DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings $shop_settings)
    {
        return new \DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlRateRequestBuilder($settings, $shipment, $shop_settings);
    }
    /**
     * Creates rate filter by currency.
     *
     * @param ShipmentRating $rating .
     *
     * @return DhlRateCurrencyFilter .
     */
    protected function create_filter_rates_by_currency(\DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating $rating)
    {
        return new \DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\DhlRateCurrencyFilter($rating, $this->shop_settings);
    }
    /**
     * Verify currency.
     *
     * @param string $default_shop_currency Shop currency.
     * @param string $checkout_currency Checkout currency.
     *
     * @return void
     * @throws CurrencySwitcherException .
     */
    protected function verify_currency($default_shop_currency, $checkout_currency)
    {
        if ($default_shop_currency !== $checkout_currency) {
            throw new \DhlVendor\WPDesk\DhlExpressShippingService\Exception\CurrencySwitcherException($this->shop_settings);
        }
    }
    /**
     * Should I use a test API?
     *
     * @param \WPDesk\AbstractShipping\Settings\SettingsValues $settings Settings.
     *
     * @return bool
     */
    public function is_testing(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings)
    {
        $testing = \false;
        if ($settings->has_value('testing') && $this->shop_settings->is_testing()) {
            $testing = 'yes' === $settings->get_value('testing') ? \true : \false;
        }
        return $testing;
    }
    /**
     * Get settings
     *
     * @return DhlSettingsDefinition
     */
    public function get_settings_definition()
    {
        return new \DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition($this->shop_settings);
    }
    /**
     * Get unique ID.
     *
     * @return string
     */
    public function get_unique_id()
    {
        return self::UNIQUE_ID;
    }
    /**
     * Get name.
     *
     * @return string
     */
    public function get_name()
    {
        return \__('DHL Express', 'flexible-shipping-dhl-express');
    }
    /**
     * Get description.
     *
     * @return string
     */
    public function get_description()
    {
        return \__('DHL Express integration', 'flexible-shipping-dhl-express');
    }
    /**
     * Pings API.
     * Returns empty string on success or error message on failure.
     *
     * @param SettingsValues  $settings .
     * @param LoggerInterface $logger .
     * @return string
     */
    public function check_connection(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \Psr\Log\LoggerInterface $logger)
    {
        try {
            $connection_checker = new \DhlVendor\WPDesk\DhlExpressShippingService\DhlApi\ConnectionChecker($settings, $logger, $this->is_testing($settings));
            $connection_checker->check_connection();
            return '';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * Returns field ID after which API Status field should be added.
     *
     * @return string
     */
    public function get_field_before_api_status_field()
    {
        return \DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_API_PASSWORD;
    }
}
