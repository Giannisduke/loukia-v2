<?php

namespace DhlVendor\WPDesk\WooCommerceShipping\CustomFields;

/**
 * Factory that can create custom fields and render footer of fields thaw were created.
 *
 * @package WPDesk\WooCommerceShipping\CustomFields
 */
interface FieldsFactory
{
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
    public function create_field($type, $data);
    /**
     * Returns true if field type is supported by factory and can be created.
     *
     * @param string $type Field type - the name that can be used in WC settings.
     *
     * @return bool
     */
    public function is_field_supported($type);
    /**
     * Factory should remember all created fields so it can render all used fields footers.
     *
     * @return string
     */
    public function render_used_fields_footers();
}
