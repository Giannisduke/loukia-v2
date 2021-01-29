<?php

/**
 * Custom fields: FieldServices class.
 *
 * @package WPDesk\WooCommerceShipping\CustomFields
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\CustomFields\Services;

use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\CustomField;
/**
 * Render view for custom services field
 *
 * @package WPDesk\CustomFields
 */
class FieldServices implements \DhlVendor\WPDesk\WooCommerceShipping\CustomFields\CustomField
{
    const FIELD_TYPE = 'services';
    /**
     * Services.
     *
     * @var array
     */
    private $services;
    /**
     * FieldServices constructor.
     *
     * @param array $services .
     */
    public function __construct(array $services)
    {
        $this->services = $services;
    }
    /**
     * Unique field name.
     *
     * @return string .
     */
    public static function get_type_name()
    {
        return self::FIELD_TYPE;
    }
    /**
     * Can sanitize data so it can be saved into DB.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function sanitize(array $data = null)
    {
        $sanitizer = new \DhlVendor\WPDesk\WooCommerceShipping\CustomFields\Services\FieldServicesSanitizer();
        return $sanitizer->sanitize_services($data);
    }
    /**
     * Sort services.
     *
     * @param array $options Services from field definition.
     * @param array $values  Services from settings.
     *
     * @return array
     */
    private function sort_services($options, $values)
    {
        foreach ($values as $key => $value) {
            if (!isset($options[$key])) {
                unset($values[$key]);
            }
        }
        foreach ($options as $key => $service) {
            if (!isset($values[$key])) {
                $values[$key] = $service;
            }
        }
        return $values;
    }
    /**
     * Render view.
     *
     * @param array|null $params Params.
     *
     * @return string.
     */
    public function render(array $params = null)
    {
        $services = $this->services;
        if (empty($params['class'])) {
            $params['class'] = '';
        }
        if (!empty($params['value'])) {
            if (!\is_array($params['value'])) {
                $params['value'] = array();
            }
            $services = $this->sort_services($services, $params['value']);
        }
        \ob_start();
        include __DIR__ . '/views/services.php';
        return \ob_get_clean();
    }
    /**
     * Field can render some data after all fields was successfully rendered.
     *
     * @param string $key Rendered field key/name.
     *
     * @return string|void Rendered footer.
     */
    public function render_footer($key)
    {
    }
}
