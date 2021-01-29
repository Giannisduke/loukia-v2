<?php

namespace DhlVendor\WPDesk\DhlExpressShippingService\DhlApi;

use DhlVendor\DHL\Datatype\AM\PieceType;
use DhlVendor\DHL\Entity\AM\GetQuote;
use DhlVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Address;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Package;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Weight;
use DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings;
use DhlVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition;
/**
 * Build request for Dhl rate
 *
 * @package WPDesk\DhlExpressShippingService\DhlApi
 */
class DhlRateRequestBuilder
{
    const MINIMAL_PACKAGE_WEIGHT = 0.001;
    const WEIGHT_ROUNDING_PRECISION = 3;
    const DIMENSION_ROUNDING_PRECISION = 3;
    /**
     * WooCommerce shipment.
     *
     * @var Shipment
     */
    private $shipment;
    /**
     * Settings values.
     *
     * @var SettingsValues
     */
    private $settings;
    /**
     * Request
     *
     * @var GetQuote
     */
    private $request;
    /**
     * Shop settings.
     *
     * @var ShopSettings
     */
    private $shop_settings;
    /**
     * DhlRateRequestBuilder constructor.
     *
     * @param SettingsValues $settings Settings.
     * @param Shipment $shipment Shipment.
     * @param ShopSettings $helper Helper.
     */
    public function __construct(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment, \DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings $helper)
    {
        $this->settings = $settings;
        $this->shipment = $shipment;
        $this->shop_settings = $helper;
        $this->request = new \DhlVendor\DHL\Entity\AM\GetQuote();
    }
    /**
     * Set authentication Dhl credentials
     */
    private function set_credentials()
    {
        $this->request->SiteID = $this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_SITE_ID);
        $this->request->Password = $this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_API_PASSWORD);
    }
    /**
     * Set shipper address
     */
    private function set_shipper_address()
    {
        if ($this->shipment->ship_from->address instanceof \DhlVendor\WPDesk\AbstractShipping\Shipment\Address) {
            $ship_from = $this->shipment->ship_from->address;
            $this->request->From->CountryCode = $ship_from->country_code;
            $this->request->From->Postalcode = $ship_from->postal_code;
            $this->request->From->City = $ship_from->city;
        }
    }
    /**
     * Set recipient address
     */
    private function set_recipient_address()
    {
        if ($this->shipment->ship_to->address instanceof \DhlVendor\WPDesk\AbstractShipping\Shipment\Address) {
            $ship_to = $this->shipment->ship_to->address;
            $this->request->To->City = $ship_to->city;
            $this->request->To->Postalcode = $ship_to->postal_code;
            $this->request->To->CountryCode = $ship_to->country_code;
        }
    }
    /**
     * Create Dhl package RequestedPackageLineItem from shipment package.
     *
     * @param Package $package
     * @param int $number
     *
     * @return PieceType
     * @throws UnitConversionException
     */
    private function create_piece_from_package(\DhlVendor\WPDesk\AbstractShipping\Shipment\Package $package, $number)
    {
        $piece = new \DhlVendor\DHL\Datatype\AM\PieceType();
        $piece->PieceID = $number;
        if ($package->weight instanceof \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight) {
            $this->set_weight($piece, $package->weight);
        }
        if ($package->dimensions instanceof \DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions) {
            $piece->Height = \round($package->dimensions->height, self::DIMENSION_ROUNDING_PRECISION);
            $piece->Depth = \round($package->dimensions->length, self::DIMENSION_ROUNDING_PRECISION);
            $piece->Width = \round($package->dimensions->width, self::DIMENSION_ROUNDING_PRECISION);
        }
        return $piece;
    }
    /**
     * Set package item.
     *
     * @throws \Exception Measure converter exception.
     */
    private function set_items()
    {
        $counter = 1;
        foreach ($this->shipment->packages as $package) {
            $this->request->BkgDetails->addPiece($this->create_piece_from_package($package, $counter++));
        }
    }
    /**
     * Returns weight unit in which DHL request would be sent.
     *
     * @return string
     */
    private function get_target_weight_unit()
    {
        $unit = $this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_UNITS, \DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::UNITS_METRIC);
        return $unit === \DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::UNITS_METRIC ? \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_KG : \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB;
    }
    /**
     * Set weight.
     *
     * @param PieceType $piece Package.
     * @param Weight $itemWeight Weight.
     *
     * @return PieceType
     * @throws UnitConversionException Unit conversion exception.
     */
    private function set_weight(\DhlVendor\DHL\Datatype\AM\PieceType $piece, \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight $itemWeight)
    {
        $target_weight_unit = $this->get_target_weight_unit();
        try {
            $weight = (new \DhlVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight($itemWeight->weight, $itemWeight->weight_unit))->as_unit_rounded($target_weight_unit);
            $piece->Weight = \round($weight >= self::MINIMAL_PACKAGE_WEIGHT ? $weight : self::MINIMAL_PACKAGE_WEIGHT, self::WEIGHT_ROUNDING_PRECISION);
        } catch (\Throwable $e) {
            throw new \DhlVendor\WPDesk\AbstractShipping\Exception\UnitConversionException($e->getMessage());
        } catch (\Exception $e) {
            // required fallback from Throwable in PHP 5.6
            throw new \DhlVendor\WPDesk\AbstractShipping\Exception\UnitConversionException($e->getMessage());
        }
        return $piece;
    }
    /**
     * Set additional request data.
     */
    private function set_additional_data()
    {
        $this->request->MessageTime = \date('Y-m-d\\TH:i:sP');
        $this->request->MessageReference = \substr(\str_shuffle(\str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', \mt_rand(1, 10))), 1, 32);
        if ($this->shipment->ship_from->address instanceof \DhlVendor\WPDesk\AbstractShipping\Shipment\Address) {
            $this->request->BkgDetails->PaymentCountryCode = $this->shipment->ship_from->address->country_code;
        }
        /** @see https://wpdesk.myjetbrains.com/youtrack/issue/PRD-1150 */
        $this->request->BkgDetails->NetworkTypeCode = 'AL';
    }
    /**
     * Set payer account number.
     */
    private function set_payment_account_number()
    {
        if ('yes' === $this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_USE_PAYMENT_ACCOUNT_NUMBER, 'no')) {
            $this->request->BkgDetails->PaymentAccountNumber = $this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_PAYMENT_ACCOUNT_NUMBER);
        }
    }
    /**
     * Set shipment date.
     */
    protected function set_shipment_date()
    {
        $this->request->BkgDetails->Date = \date('Y-m-d');
        $this->request->BkgDetails->ReadyTime = \sprintf('PT%1$sH%2$sM', \date('H'), \date('i'));
        $this->request->BkgDetails->ReadyTimeGMTOffset = \date('P');
    }
    private function set_units()
    {
        if ($this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_UNITS, \DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::UNITS_METRIC) === \DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::UNITS_METRIC) {
            $this->request->BkgDetails->DimensionUnit = \DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_CM;
            $this->request->BkgDetails->WeightUnit = \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_KG;
        } else {
            $this->request->BkgDetails->DimensionUnit = \DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_IN;
            $this->request->BkgDetails->WeightUnit = \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB;
        }
    }
    /**
     * Calculate shipment value.
     *
     * @return float
     */
    private function calculate_shipment_value()
    {
        $shipment_value = 0.0;
        foreach ($this->shipment->packages as $package) {
            foreach ($package->items as $item) {
                $shipment_value += $item->declared_value->amount;
            }
        }
        return \round($shipment_value, $this->shop_settings->get_price_rounding_precision());
    }
    /**
     * Set insurance.
     */
    private function set_insurance()
    {
        if ('yes' === $this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_INSURANCE, 'no')) {
            $this->request->BkgDetails->InsuredValue = $this->calculate_shipment_value();
            $this->request->BkgDetails->InsuredCurrency = $this->shop_settings->get_currency();
        }
    }
    /**
     * Build request.
     * @throws \Exception
     */
    public function build_request()
    {
        $this->set_credentials();
        $this->set_shipper_address();
        $this->set_recipient_address();
        $this->set_items();
        $this->set_additional_data();
        $this->set_shipment_date();
        $this->set_units();
        $this->set_insurance();
        $this->set_payment_account_number();
        return $this->request;
    }
}
