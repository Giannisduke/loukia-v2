<?php

/**
 * Connection checker.
 *
 * @package WPDesk\DhlShippingService\DhlApi
 */
namespace DhlVendor\WPDesk\DhlExpressShippingService\DhlApi;

use DhlVendor\DHL\Client\Web;
use DhlVendor\DHL\Datatype\AM\PieceType;
use DhlVendor\DHL\Entity\AM\GetQuote;
use DhlVendor\DHL\Entity\AM\GetQuoteResponse;
use Psr\Log\LoggerInterface;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition;
/**
 * Can check connection.
 */
class ConnectionChecker
{
    /**
     * Settings.
     *
     * @var SettingsValues
     */
    private $settings;
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /** @var bool */
    private $is_testing;
    /**
     * ConnectionChecker constructor.
     *
     * @param SettingsValues  $settings .
     * @param LoggerInterface $logger .
     * @param bool $is_testing .
     */
    public function __construct(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \Psr\Log\LoggerInterface $logger, $is_testing)
    {
        $this->settings = $settings;
        $this->logger = $logger;
        $this->is_testing = $is_testing;
    }
    /**
     * @param string $site_id .
     * @param string $password .
     *
     * @return GetQuote
     * @throws \Exception
     */
    private function create_quote($site_id, $password)
    {
        $sample = new \DhlVendor\DHL\Entity\AM\GetQuote();
        $sample->SiteID = $site_id;
        $sample->Password = $password;
        $sample->MessageTime = '2001-12-17T09:30:47-05:00';
        $sample->MessageReference = 'reference_28_to_32_chars_1234567';
        $sample->BkgDetails->Date = \date('Y-m-d');
        $sample->BkgDetails->PaymentCountryCode = 'GB';
        $sample->BkgDetails->DimensionUnit = 'CM';
        $sample->BkgDetails->WeightUnit = 'KG';
        $sample->BkgDetails->ReadyTime = 'PT10H21M';
        $sample->BkgDetails->ReadyTimeGMTOffset = '+01:00';
        $piece = new \DhlVendor\DHL\Datatype\AM\PieceType();
        $piece->PieceID = 1;
        $piece->Height = 10;
        $piece->Depth = 10;
        $piece->Width = 10;
        $piece->Weight = 10;
        $sample->BkgDetails->addPiece($piece);
        $sample->From->CountryCode = 'GB';
        $sample->From->Postalcode = 'DD13JA';
        $sample->To->City = 'Herndon';
        $sample->To->Postalcode = '20171';
        $sample->To->CountryCode = 'US';
        $sample->BkgDetails->IsDutiable = 'N';
        return $sample;
    }
    /**
     * Pings API.
     * Throws exception on failure.
     *
     * @return void
     * @throws \Exception .
     */
    public function check_connection()
    {
        $mode = 'production';
        if ($this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_TESTING) === 'yes') {
            $mode = 'staging';
        }
        $client = new \DhlVendor\DHL\Client\Web($mode);
        $xml_response = $client->call($this->create_quote($this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_SITE_ID), $this->settings->get_value(\DhlVendor\WPDesk\DhlExpressShippingService\DhlSettingsDefinition::FIELD_API_PASSWORD)));
        $response = new \DhlVendor\DHL\Entity\AM\GetQuoteResponse();
        $response->initFromXML($xml_response);
    }
}
