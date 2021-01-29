<?php

/**
 * Meta data interpreter implementation.
 *
 * @package WPDesk\WooCommerceShipping\OrderMetaData
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData;

/**
 * Can interpret meta data from WooCommerce order shipping on admin.
 */
class SingleAdminOrderMetaDataInterpreterImplementation implements \DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreter
{
    use AdminMetaDataUnchangedTrait;
    /**
     * Key.
     *
     * @var string
     */
    private $key;
    /**
     * Display key.
     *
     * @var string
     */
    private $label;
    /**
     * @param string $key ,
     * @param string $label .
     */
    public function __construct($key, $label)
    {
        $this->key = $key;
        $this->label = $label;
    }
    /**
     * Get meta key on admin order edit page.
     *
     * @param string         $display_key .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_display_key($display_key, $meta, $order_item)
    {
        return $this->label;
    }
    /**
     * Is supported key on admin?
     *
     * @param string $display_key .
     *
     * @return bool
     */
    public function is_supported_key_on_admin($display_key)
    {
        return $this->key === $display_key;
    }
}
