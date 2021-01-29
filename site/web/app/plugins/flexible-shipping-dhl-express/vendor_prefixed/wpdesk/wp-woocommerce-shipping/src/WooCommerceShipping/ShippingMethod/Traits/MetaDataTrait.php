<?php

namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits;

use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
trait MetaDataTrait
{
    /**
     * Meta data builder.
     *
     * @var WooCommerceShippingMetaDataBuilder
     */
    private $metadata_builder;
    /**
     * Set metadata builder.
     *
     * @param WooCommerceShippingMetaDataBuilder $metadata_builder .
     */
    public function set_meta_data_builder(\DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder $metadata_builder)
    {
        $this->metadata_builder = $metadata_builder;
    }
}
