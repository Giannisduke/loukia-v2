<?php

namespace DhlVendor\WPDesk\WooCommerceShippingPro\ProShippingMethod;

use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\CustomField;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\MethodFieldsFactory;
use DhlVendor\WPDesk\WooCommerceShippingPro\CustomFields\ShippingBoxes;
use DhlVendor\WPDesk\Packer\BoxFactory\BoxesWithUnit;
/**
 * Field factory that can create pro fields.
 *
 * @package WPDesk\WooCommerceShippingPro
 */
class ProMethodFieldsFactory extends \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\MethodFieldsFactory
{
    /** @var \WC_Shipping_Method */
    private $method;
    /** @var BoxesWithUnit */
    private $boxes;
    public function __construct(\WC_Shipping_Method $method, \DhlVendor\WPDesk\Packer\BoxFactory\BoxesWithUnit $boxes)
    {
        $this->method = $method;
        $this->boxes = $boxes;
    }
    /**
     * Create field - factory method.
     *
     * @param string $type Field type.
     * @param array $data Field data.
     *
     * @return CustomField
     * @throws \Exception View doesn't exists.
     *
     */
    public function create_field($type, $data)
    {
        if ($type === \DhlVendor\WPDesk\WooCommerceShippingPro\CustomFields\ShippingBoxes::get_type_name()) {
            $key = isset($data['field_key']) ? $data['field_key'] : $type;
            return $this->remember_creation(new \DhlVendor\WPDesk\WooCommerceShippingPro\CustomFields\ShippingBoxes($this->method, $this->boxes), $key);
        }
        return parent::create_field($type, $data);
    }
    /**
     * Returns true if field type is supported by factory and can be created.
     *
     * @param string $type Field type - the name that can be used in WC settings.
     *
     * @return bool
     */
    public function is_field_supported($type)
    {
        return \in_array($type, [\DhlVendor\WPDesk\WooCommerceShippingPro\CustomFields\ShippingBoxes::get_type_name()], \true) || parent::is_field_supported($type);
    }
}
