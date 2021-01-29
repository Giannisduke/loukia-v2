<?php

namespace DhlVendor\DHL\Entity\AM;

use DhlVendor\WPDesk\DhlExpressShippingService\Exception\DhlApiException;
class GetQuoteResponse
{
    /**
     * @var \SimpleXMLElement
     */
    protected $Response;
    /**
     * @var \SimpleXMLElement
     */
    protected $BkgDetails;
    /**
     * @var \SimpleXMLElement
     */
    protected $Srvs;
    /**
     * Initialize object from an XML string
     *
     * @param string $xml XML String
     *
     * @return void
     * @throws \Exception Exception thrown if response returned has an error
     */
    public function initFromXML($xml)
    {
        $xml = \simplexml_load_string($xml);
        if (isset($xml->Response, $xml->Response->Status, $xml->Response->Status->Condition, $xml->Response->Status->Condition->ConditionCode) && (string) $xml->Response->Status->Condition->ConditionCode != '') {
            throw new \DhlVendor\WPDesk\DhlExpressShippingService\Exception\DhlApiException(\esc_html($xml->Response->Status->Condition->ConditionData), (int) $xml->Response->Status->Condition->ConditionCode);
        }
        if (isset($xml->GetQuoteResponse, $xml->GetQuoteResponse->Note, $xml->GetQuoteResponse->Note->Condition)) {
            foreach ($xml->GetQuoteResponse->Note->Condition as $condition) {
                throw new \DhlVendor\WPDesk\DhlExpressShippingService\Exception\DhlApiException(\esc_html($condition->ConditionData), (int) $condition->ConditionCode);
            }
        }
        $this->Response = $xml->GetQuoteResponse->Response;
        $this->BkgDetails = $xml->GetQuoteResponse->BkgDetails;
        $this->Srvs = $xml->GetQuoteResponse->Srvs;
    }
    /**
     * @return \SimpleXMLElement
     */
    public function getResponse()
    {
        return $this->Response;
    }
    /**
     * @return \SimpleXMLElement
     */
    public function getBkgDetails()
    {
        return $this->BkgDetails;
    }
    /**
     * @return \SimpleXMLElement
     */
    public function getSrvs()
    {
        return $this->Srvs;
    }
}
