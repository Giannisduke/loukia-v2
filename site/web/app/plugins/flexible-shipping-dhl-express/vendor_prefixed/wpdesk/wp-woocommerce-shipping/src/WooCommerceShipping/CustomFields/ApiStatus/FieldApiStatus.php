<?php

/**
 * Custom fields: FieldApiStatus class.
 *
 * @package WPDesk\WooCommerceShipping\CustomFields
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus;

use DhlVendor\WPDesk\AbstractShipping\ShippingService;
use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\CustomField;
/**
 * Render view for custom services field
 *
 * @package WPDesk\CustomFields
 */
class FieldApiStatus implements \DhlVendor\WPDesk\WooCommerceShipping\CustomFields\CustomField
{
    const SECURITY_NONCE = 'security_nonce';
    const SHIPPING_SERVICE_ID = 'shipping_service_id';
    /**
     * Shipping service id.
     *
     * @var string
     */
    private $shipping_service_id;
    /**
     * Security nonce.
     *
     * @var string
     */
    private $security_nonce;
    /**
     * FieldApiStatus constructor.
     *
     * @param string $shipping_service_id .
     * @param string $security_nonce .
     */
    public function __construct($shipping_service_id, $security_nonce)
    {
        $this->shipping_service_id = $shipping_service_id;
        $this->security_nonce = $security_nonce;
    }
    /**
     * Unique field name.
     *
     * @return string .
     */
    public static function get_type_name()
    {
        return 'api_status';
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
        return null;
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
        $field_id = $params['field_key'];
        $title = $params['title'];
        $tooltip = '';
        if (\true === $params['desc_tip']) {
            $tooltip = $params['description'];
        } elseif (!empty($data['desc_tip'])) {
            $tooltip = $data['desc_tip'];
        }
        $description = '';
        $default = $params['default'];
        $class = $params['class'];
        $css = $params['css'];
        $security_nonce = $this->security_nonce;
        $shipping_service_id = $this->shipping_service_id;
        $ajax_url = \admin_url('admin-ajax.php');
        \ob_start();
        include __DIR__ . '/views/api-status.php';
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
