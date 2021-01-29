<?php

namespace DhlVendor\WPDesk\DhlExpressShippingService;

use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings;
/**
 * A class that defines the basic settings for the shipping method.
 *
 * @package WPDesk\DhlShippingService
 */
class DhlSettingsDefinition extends \DhlVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition
{
    const CUSTOM_SERVICES_CHECKBOX_CLASS = 'wpdesk_wc_shipping_custom_service_checkbox';
    const FIELD_TYPE_FALLBACK = 'fallback';
    const FIELD_SERVICES_TABLE = 'services';
    const FIELD_ENABLE_CUSTOM_SERVICES = 'enable_custom_services';
    const FIELD_INSURANCE = 'insurance';
    const FIELD_FALLBACK = 'fallback';
    const FIELD_UNITS = 'units';
    const UNITS_IMPERIAL = 'imperial';
    const UNITS_METRIC = 'metric';
    const RATE_ADJUSTMENTS_TITLE = 'rate_adjustments_title';
    const FIELD_API_PASSWORD = 'api_password';
    const FIELD_USE_PAYMENT_ACCOUNT_NUMBER = 'use_payment_account_number';
    const FIELD_PAYMENT_ACCOUNT_NUMBER = 'payment_account_number';
    const FIELD_SITE_ID = 'site_id';
    const FIELD_TESTING = 'testing';
    const FIELD_PACKING_METHOD = 'packing_method';
    const PACKING_METHOD_WEIGHT = 'weight';
    const PACKING_METHOD_SEPARATELY = 'separately';
    /**
     * Shop settings.
     *
     * @var ShopSettings
     */
    private $shop_settings;
    /**
     * DhlSettingsDefinition constructor.
     *
     * @param ShopSettings $shop_settings Shop settings.
     */
    public function __construct(\DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings $shop_settings)
    {
        $this->shop_settings = $shop_settings;
    }
    /**
     * Validate settings.
     *
     * @param SettingsValues $settings Settings.
     *
     * @return bool
     */
    public function validate_settings(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings)
    {
        return \true;
    }
    /**
     * Get units default.
     *
     * @return string
     */
    private function get_units_default()
    {
        $weight_unit = $this->shop_settings->get_weight_unit();
        if (\in_array($weight_unit, array('g', 'kg'), \true)) {
            return self::UNITS_METRIC;
        }
        return self::UNITS_IMPERIAL;
    }
    /**
     * Initialise Settings Form Fields.
     */
    public function get_form_fields()
    {
        $dhl_services = new \DhlVendor\WPDesk\DhlExpressShippingService\DhlServices();
        $services = $dhl_services->get_grouped_services();
        $connection_fields = ['dhl_header' => ['title' => \__('DHL Express', 'flexible-shipping-dhl-express'), 'type' => 'title'], 'credentials_header' => ['title' => \__('Credentials', 'flexible-shipping-dhl-express'), 'type' => 'title', 'description' => \sprintf(
            // Translators: link.
            \__('You need to provide DHL Express account credentials to get live rates. Learn %1$show to create a DHL Express account →%2$s', 'flexible-shipping-dhl-express'),
            '<a href=" ' . $this->prepare_create_account_docs_link() . ' " target="_blank">',
            '</a>'
        )], self::FIELD_SITE_ID => ['title' => \__('Site ID', 'flexible-shipping-dhl-express'), 'type' => 'text', 'custom_attributes' => ['required' => 'required']], self::FIELD_API_PASSWORD => ['title' => \__('Password', 'flexible-shipping-dhl-express'), 'type' => 'password', 'custom_attributes' => ['required' => 'required'], 'description' => \sprintf(
            // Translators: HTML strong tag and link.
            \__('In order to get the %1$sSite ID%2$s log in to your DHL Express account %3$shere%4$s and copy it from %1$sMy profile%2$s or %1$sXML Services Status%2$s tabs. If you haven\'t received an email containing the %1$sSite ID%2$s and %1$spassword%2$s, please contact the DHL Express support directly.', 'flexible-shipping-dhl-express'),
            '<strong>',
            '</strong>',
            '<a href="https://xmlportal.dhl.com/login" target="_blank">',
            '</a>'
        )]];
        if ($this->shop_settings->is_testing()) {
            $connection_fields[self::FIELD_TESTING] = ['title' => \__('Test Credentials', 'flexible-shipping-dhl-express'), 'type' => 'checkbox', 'label' => \__('Enable to use test credentials', 'flexible-shipping-dhl-express'), 'desc_tip' => \true];
        }
        $custom_fields = ['shipping_method_header' => ['title' => \__('Method Settings', 'flexible-shipping-dhl-express'), 'type' => 'title', 'description' => \__('Set how DHL Express services are displayed.', 'flexible-shipping-dhl-express')], 'enable_shipping_method' => ['title' => \__('Enable/Disable', 'flexible-shipping-dhl-express'), 'type' => 'checkbox', 'label' => \__('Enable DHL Express global shipping method', 'flexible-shipping-dhl-express'), 'description' => \__('If you need to turn off DHL Express rates display in the shop, just uncheck this option.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true, 'default' => 'yes'], 'title' => ['title' => \__('Method Title', 'flexible-shipping-dhl-express'), 'type' => 'text', 'description' => \__('This controls the title which the user sees during checkout when fallback is used.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true, 'default' => \__('DHL Express', 'flexible-shipping-dhl-express')], self::FIELD_FALLBACK => ['title' => self::FIELD_FALLBACK, 'type' => self::FIELD_FALLBACK], self::FIELD_ENABLE_CUSTOM_SERVICES => ['title' => \__('Custom Services', 'flexible-shipping-dhl-express'), 'type' => 'checkbox', 'label' => \__('Enable custom services', 'flexible-shipping-dhl-express'), 'description' => \__('Enable if you want to select available services. By enabling a service, it does not guarantee that it will be offered, as the plugin will only offer the available rates based on the package weight, the origin and the destination.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true, 'class' => self::CUSTOM_SERVICES_CHECKBOX_CLASS], self::FIELD_SERVICES_TABLE => ['title' => \__('Services Table', 'flexible-shipping-dhl-express'), 'type' => 'services', 'options' => $services], self::RATE_ADJUSTMENTS_TITLE => ['title' => \__('Rates Adjustments', 'flexible-shipping-dhl-express'), 'description' => \__('Adjust these settings to get more accurate rates.', 'flexible-shipping-dhl-express'), 'type' => 'title'], self::FIELD_USE_PAYMENT_ACCOUNT_NUMBER => ['title' => \__('Discounted Rates', 'flexible-shipping-dhl-express'), 'type' => 'checkbox', 'label' => \__('Enable if you want to use discounted rates', 'flexible-shipping-dhl-express'), 'description' => \__('If you want to use the rates assigned to your account, use the ID assigned to the payer\'s account. Contact DHL Express for more information.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true], self::FIELD_PAYMENT_ACCOUNT_NUMBER => ['title' => \__('Payment Account Number', 'flexible-shipping-dhl-express'), 'type' => 'text'], self::FIELD_INSURANCE => ['title' => \__('Insurance', 'flexible-shipping-dhl-express'), 'type' => 'checkbox', 'label' => \__('Request insurance to be included in DHL Express rates', 'flexible-shipping-dhl-express'), 'description' => \__('Enable if you want to include insurance in DHL Express rates when it is available.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true], self::FIELD_PACKING_METHOD => ['title' => \__('Parcel Packing Method', 'flexible-shipping-dhl-express'), 'type' => 'select', 'options' => array(self::PACKING_METHOD_WEIGHT => \__('Pack into one box by weight', 'flexible-shipping-dhl-express'), self::PACKING_METHOD_SEPARATELY => \__('Pack items separately', 'flexible-shipping-dhl-express')), 'description' => \__('This option allows you to achieve more accurate Shipping Rates.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true, 'default' => self::PACKING_METHOD_WEIGHT], 'advanced_options_header' => ['title' => \__('Advanced Options', 'flexible-shipping-dhl-express'), 'type' => 'title'], 'debug_mode' => ['title' => \__('Debug Mode', 'flexible-shipping-dhl-express'), 'label' => \__('Enable debug mode', 'flexible-shipping-dhl-express'), 'type' => 'checkbox', 'description' => \__('Enable debug mode to display messages in the cart/checkout. Only admins and shop managers will see all messages and data sent to DHL Express. The customer will only see messages from the DHL Express API.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true], self::FIELD_UNITS => ['title' => \__('Measurement Units', 'flexible-shipping-dhl-express'), 'type' => 'select', 'options' => array(self::UNITS_IMPERIAL => \__('LBS/IN', 'flexible-shipping-dhl-express'), self::UNITS_METRIC => \__('KG/CM', 'flexible-shipping-dhl-express')), 'description' => \__('By default store settings are used. If you see "This measurement system is not valid for the selected country" errors, switch units. Units in the store settings will be converted to units required by DHL Express.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true, 'default' => $this->get_units_default()]];
        return \array_replace($connection_fields, $custom_fields);
    }
    /**
     * Prepare create account docs link.
     */
    private function prepare_create_account_docs_link()
    {
        return \get_locale() === 'pl_PL' ? 'https://docs.flexibleshipping.com/article/422-dhl-express-how-to-create-an-account?utm_source=dhl-express-settings-pl&utm_medium=link&utm_campaign=dhl-express-credentials' : 'https://docs.flexibleshipping.com/article/422-dhl-express-how-to-create-an-account?utm_source=dhl-express-settings&utm_medium=link&utm_campaign=dhl-express-credentials';
    }
}
