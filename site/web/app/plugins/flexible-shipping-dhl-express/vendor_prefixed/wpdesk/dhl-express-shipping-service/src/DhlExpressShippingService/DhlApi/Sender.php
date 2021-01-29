<?php

namespace DhlVendor\WPDesk\DhlExpressShippingService\DhlApi;

use DhlVendor\DHL\Entity\AM\GetQuote;
use DhlVendor\DHL\Entity\AM\GetQuoteResponse;
/**
 * Sender class interface.
 *
 * @package WPDesk\DhlExpressShippingService\DhlApi
 */
interface Sender
{
    /**
     * Send request.
     *
     * @param GetQuote $request Request.
     *
     * @return GetQuoteResponse
     */
    public function send(\DhlVendor\DHL\Entity\AM\GetQuote $request);
}
