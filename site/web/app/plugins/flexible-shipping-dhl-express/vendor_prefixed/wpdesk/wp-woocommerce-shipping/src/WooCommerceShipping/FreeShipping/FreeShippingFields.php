<?php

namespace DhlVendor\WPDesk\WooCommerceShipping\FreeShipping;

/**
 * Can replace fake free_shipping field with custom free shipping fields to shipping method settings fields.
 *
 * @package WPDesk\WooCommerceShipping\FreeShipping
 */
class FreeShippingFields
{
    const FIELD_TYPE_FREE_SHIPPING = 'free_shipping';
    const FIELD_STATUS = 'free_shipping_status';
    const FIELD_AMOUNT = 'free_shipping_amount';
    /**
     * Replace free_shipping fake field with checkbox and input fields in settings.
     *
     * @param array $settings
     *
     * @return array
     */
    public function replace_fields(array $settings)
    {
        $new_settings = [];
        foreach ($settings as $key => $field) {
            if ($field['type'] === self::FIELD_TYPE_FREE_SHIPPING) {
                $new_settings[self::FIELD_STATUS] = ['title' => \__('Free Shipping', 'flexible-shipping-dhl-express'), 'type' => 'checkbox', 'label' => \__('Enable free shipping', 'flexible-shipping-dhl-express'), 'description' => '', 'desc_tip' => \true, 'default' => 'no'];
                $new_settings[self::FIELD_AMOUNT] = ['title' => \__('Free Shipping Amount', 'flexible-shipping-dhl-express'), 'type' => 'price', 'required' => \true, 'description' => \__('Enter a minimum order amount for free shipment.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true, 'default' => ''];
            } else {
                $new_settings[$key] = $field;
            }
        }
        return $new_settings;
    }
}
