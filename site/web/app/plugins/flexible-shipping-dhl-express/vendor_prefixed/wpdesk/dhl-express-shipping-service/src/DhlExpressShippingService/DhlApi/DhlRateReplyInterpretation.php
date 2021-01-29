<?php

namespace DhlVendor\WPDesk\DhlExpressShippingService\DhlApi;

use DhlVendor\DHL\Entity\AM\GetQuoteResponse;
use DhlVendor\WPDesk\AbstractShipping\Rate\Money;
use DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use DhlVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Get response from API
 *
 * @package WPDesk\DhlExpressShippingService\DhlApi
 */
class DhlRateReplyInterpretation implements \DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating
{
    /**
     * Is tax enabled.
     *
     * @var bool
     */
    private $is_tax_enabled;
    /**
     * Reply.
     *
     * @var GetQuoteResponse
     */
    private $reply;
    /**
     * @var string
     */
    private $shop_default_currency;
    /**
     * DhlRateReplyInterpretation constructor.
     *
     * @param GetQuoteResponse $reply Rate reply.
     * @param bool $is_tax_enabled Is tax enabled.
     * @param string $shop_default_currency Shop default currency.
     */
    public function __construct(\DhlVendor\DHL\Entity\AM\GetQuoteResponse $reply, $is_tax_enabled, $shop_default_currency)
    {
        $this->reply = $reply;
        $this->is_tax_enabled = $is_tax_enabled;
        $this->shop_default_currency = $shop_default_currency;
    }
    /**
     * Get single rate.
     *
     * @param \SimpleXMLElement $single_quote .
     *
     * @return SingleRate
     */
    protected function get_single_rate($single_quote)
    {
        $rate = new \DhlVendor\WPDesk\AbstractShipping\Rate\SingleRate();
        $rate->service_type = (string) $single_quote->GlobalProductCode;
        $rate->service_name = (string) $single_quote->ProductShortName;
        $money = new \DhlVendor\WPDesk\AbstractShipping\Rate\Money();
        if ($this->is_tax_enabled) {
            $money->amount = (float) $single_quote->ShippingCharge - (float) $single_quote->TotalTaxAmount;
        } else {
            $money->amount = (float) $single_quote->ShippingCharge;
        }
        $money->currency = (string) $single_quote->CurrencyCode;
        $rate->total_charge = $money;
        return $rate;
    }
    /**
     * Get response from Dhl.
     *
     * @return SingleRate[]
     */
    public function get_ratings()
    {
        $rates = [];
        $bkg_details = $this->reply->getBkgDetails();
        if (isset($bkg_details, $bkg_details->QtdShp)) {
            foreach ($bkg_details->QtdShp as $single_quote) {
                if (0.0 !== \round((float) $single_quote->ShippingCharge, 2) && !empty((string) $single_quote->CurrencyCode)) {
                    $rates[] = $this->get_single_rate($single_quote);
                }
            }
        }
        return $rates;
    }
}
