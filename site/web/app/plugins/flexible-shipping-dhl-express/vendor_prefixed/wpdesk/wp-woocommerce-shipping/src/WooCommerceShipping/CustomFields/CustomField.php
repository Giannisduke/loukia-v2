<?php

/**
 * Custom fields: CustomField interface.
 *
 * @package WPDesk\WooCommerceShipping\CustomFields
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\CustomFields;

/**
 * Define interface for custom fields.
 *
 * @package WPDesk\CustomFields
 */
interface CustomField
{
    /**
     * Render view.
     *
     * @param array|null $params Params.
     *
     * @return mixed
     */
    public function render(array $params = null);
    /**
     * Can sanitize data so it can be saved into DB.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function sanitize(array $data = null);
    /**
     * Unique field name.
     *
     * @return string .
     */
    public static function get_type_name();
    /**
     * Field can render some data after all fields was successfully rendered.
     *
     * @param string $key Rendered field key/name.
     *
     * @return string|void Rendered footer.
     */
    public function render_footer($key);
}
