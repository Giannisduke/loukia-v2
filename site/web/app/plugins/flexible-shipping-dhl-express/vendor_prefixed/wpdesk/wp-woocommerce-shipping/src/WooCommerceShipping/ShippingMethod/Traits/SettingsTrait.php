<?php

namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits;

use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use DhlVendor\WPDesk\AbstractShipping\ShippingService;
use DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanTestSettings;
use DhlVendor\WPDesk\WooCommerceShipping\ApiStatus\ApiStatusSettingsDefinitionDecorator;
use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\Services\FieldServices;
use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees;
use DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldsFactory;
use DhlVendor\WPDesk\WooCommerceShipping\CustomOrigin\CustomOriginFields;
use DhlVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields;
use DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentNone;
use DhlVendor\WPDesk\WooCommerceShipping\PluginShippingDecisions;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasCustomOrigin;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasFreeShipping;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasHandlingFees;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\MethodFieldsFactory;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
/**
 * Job of this trait is to render/save/load settings fields using WC_Shipping_Method methods or FieldsFactory.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\Traits
 */
trait SettingsTrait
{
    /**
     * For internal caching purpose.
     *
     * @var FieldsFactory
     */
    protected $fields_factory;
    /**
     * Returns decorated settings definitions from service.
     *
     * @param PluginShippingDecisions $plugin_shipping_decisions .
     *
     * @return SettingsDefinition .
     */
    protected function get_settings_definition_from_service(\DhlVendor\WPDesk\WooCommerceShipping\PluginShippingDecisions $plugin_shipping_decisions)
    {
        $shipping_service = $plugin_shipping_decisions->get_shipping_service();
        $settings_definitions = $shipping_service->get_settings_definition();
        if ($shipping_service instanceof \DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanTestSettings) {
            $settings_definitions = new \DhlVendor\WPDesk\WooCommerceShipping\ApiStatus\ApiStatusSettingsDefinitionDecorator($settings_definitions, $shipping_service->get_field_before_api_status_field(), $plugin_shipping_decisions->get_field_api_status_ajax(), $shipping_service->get_unique_id());
        }
        return $settings_definitions;
    }
    /**
     * Returns decorated form fields if needed.
     *
     * @param PluginShippingDecisions $plugin_shipping_decisions .
     *
     * @return array
     */
    private function get_form_fields_from_shipping_service(\DhlVendor\WPDesk\WooCommerceShipping\PluginShippingDecisions $plugin_shipping_decisions)
    {
        return $this->get_settings_definition_from_service($plugin_shipping_decisions)->get_form_fields();
    }
    /**
     * Get the form fields after they are initialized.
     *
     * @return array of options
     */
    public function get_form_fields()
    {
        return $this->prepare_custom_field_types(parent::get_form_fields());
    }
    /**
     * Get settings fields for instances of this shipping method (within zones).
     */
    public function get_instance_form_fields()
    {
        return $this->prepare_custom_field_types(parent::get_instance_form_fields());
    }
    /**
     * Generate Settings HTML.
     *
     * @param array $form_fields Form fields.
     * @param bool $echo Show or return.
     *
     * @return string Generated settings
     * @throws \Exception View doesn't exists.
     *
     */
    public function generate_settings_html($form_fields = array(), $echo = \true)
    {
        if (empty($form_fields)) {
            $form_fields = $this->get_form_fields();
        }
        $settings = new \DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray($this->settings + $this->instance_settings);
        $html = '';
        foreach ($form_fields as $field_id => $values) {
            $type = $this->get_field_type($values);
            if ($settings->has_value($type)) {
                $values['value'] = $settings->get_value($type);
            }
            if (\method_exists($this, 'generate_' . $type . '_html')) {
                $html .= $this->{'generate_' . $type . '_html'}($field_id, $values);
            } elseif ($type === 'number') {
                $html .= $this->generate_text_html($field_id, $values);
            } elseif (null !== ($custom_field = $this->create_fields_factory()->create_field($type, $values))) {
                $html .= $custom_field->render($this->get_field_params($field_id, $values));
            }
        }
        if ($echo) {
            echo $html;
            // WPCS: XSS ok.
        } else {
            return $html;
        }
        return $html;
    }
    /**
     * Get a field's posted and validated value.
     *
     * @param string $key Field key.
     * @param array $field Field array.
     * @param array $post_data Posted data.
     *
     * @return string
     */
    public function get_field_value($key, $field, $post_data = array())
    {
        $type = $this->get_field_type($field);
        $field_key = $this->get_field_key($key);
        $post_data = empty($post_data) ? $_POST : $post_data;
        // WPCS: CSRF ok, input var ok.
        $value = isset($post_data[$field_key]) ? $post_data[$field_key] : null;
        if ($this->create_fields_factory()->is_field_supported($type)) {
            return $this->create_fields_factory()->create_field($type, $post_data)->sanitize($value);
        }
        return parent::get_field_value($key, $field, $post_data);
    }
    /**
     * Prepare custom field types.
     *
     * @param $fields
     *
     * @return array
     *
     * @TODO: Breaks OCP. Move to Placeholder factory.
     */
    private function prepare_custom_field_types($fields)
    {
        $fields = $this->replace_fallback_field_if_exists($fields);
        if ($this instanceof \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasHandlingFees) {
            $fields = $this->replace_handling_fees_field_if_exists($fields);
        }
        if ($this instanceof \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasCustomOrigin) {
            $custom_origin_fields = new \DhlVendor\WPDesk\WooCommerceShipping\CustomOrigin\CustomOriginFields();
            $fields = $custom_origin_fields->replace_fallback_field_if_exists($fields);
        }
        if ($this instanceof \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasFreeShipping) {
            $free_shipping_fields = new \DhlVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields();
            $fields = $free_shipping_fields->replace_fields($fields);
        }
        $fields = $this->setup_sanitize_callback_on_services_field($fields);
        return $fields;
    }
    /**
     * Always creates fields factory. Can be overwritten to change factory.
     *
     * @return FieldsFactory
     */
    protected function create_fields_factory()
    {
        if ($this->fields_factory === null) {
            $this->fields_factory = new \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\MethodFieldsFactory();
        }
        return $this->fields_factory;
    }
    /**
     * Replace fallback fake field with checkbox and input field in settings.
     *
     * @param $settings
     *
     * @return array
     */
    private function replace_fallback_field_if_exists($settings)
    {
        $new_settings = [];
        foreach ($settings as $key => $field) {
            if ($field['type'] === \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod::FIELD_TYPE_FALLBACK) {
                $new_settings[\DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod::FIELD_ENABLE_FALLBACK] = ['title' => \__('Fallback', 'flexible-shipping-dhl-express'), 'type' => 'checkbox', 'label' => \__('Enable fallback', 'flexible-shipping-dhl-express'), 'description' => \__('Enable to offer flat rate cost for shipping so that the user can still checkout, if API returns no matching rates.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true, 'default' => 'no'];
                $new_settings[\DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod::FIELD_FALLBACK_COST] = ['title' => \__('Fallback Cost', 'flexible-shipping-dhl-express'), 'type' => 'price', 'required' => \true, 'description' => \__('Enter a numeric value with no currency symbols.', 'flexible-shipping-dhl-express'), 'desc_tip' => \true, 'default' => ''];
            } else {
                $new_settings[$key] = $field;
            }
        }
        return $new_settings;
    }
    /**
     * Replace handling fees fake field with checkbox and input field in settings.
     *
     * @param array $settings Settings fields.
     *
     * @return array
     */
    private function replace_handling_fees_field_if_exists($settings)
    {
        $new_settings = [];
        foreach ($settings as $key => $field) {
            if ($field['type'] === \DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees::FIELD_TYPE) {
                $field_handling_fees = new \DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees();
                $new_settings = $field_handling_fees->add_to_settings($new_settings, $field);
            } else {
                $new_settings[$key] = $field;
            }
        }
        return $new_settings;
    }
    /**
     * Setup sanitize callback on services field.
     *
     * @param $settings
     *
     * @return mixed
     *
     * @TODO: move to custom field.
     *
     */
    private function setup_sanitize_callback_on_services_field($settings)
    {
        foreach ($settings as $key => $field) {
            if (isset($field['type']) && \DhlVendor\WPDesk\WooCommerceShipping\CustomFields\Services\FieldServices::FIELD_TYPE === $field['type']) {
                $settings[$key]['sanitize_callback'] = [\DhlVendor\WPDesk\WooCommerceShipping\CustomFields\Services\FieldServices::class, 'sanitize'];
            }
        }
        return $settings;
    }
    /**
     * Get field params
     *
     * @param string $key Field key.
     * @param array $data Data.
     *
     * @return array
     *
     * @TODO: is this really necessary?
     */
    private function get_field_params($key, $data)
    {
        $field_key = $this->get_field_key($key);
        $defaults = ['field_key' => $field_key, 'title' => '', 'disabled' => \false, 'class' => '', 'css' => '', 'placeholder' => '', 'type' => 'text', 'desc_tip' => \false, 'description' => '', 'custom_attributes' => [], 'value' => ''];
        $data = \wp_parse_args($data, $defaults);
        return $data;
    }
    /**
     * Render shipping method settings.
     *
     * @throws \Exception .
     */
    public function admin_options()
    {
        if ($this->instance_id) {
            $settings_html = $this->generate_settings_html($this->get_instance_form_fields(), \false);
        } else {
            $settings_html = $this->generate_settings_html($this->get_form_fields(), \false);
        }
        $service_id = $this->id;
        include __DIR__ . '/view/shipping-method-settings-html.php';
        echo $this->create_fields_factory()->render_used_fields_footers();
        /** @TODO: move to custom field & field footer. */
        $settings_prefix = 'woocommerce_' . $this->id;
        include __DIR__ . '/view/shipping-method-java-script-fallback.php';
        include __DIR__ . '/view/shipping-method-java-script-custom-services.php';
        include __DIR__ . '/view/shipping-method-java-script-custom-origin.php';
        if ($this instanceof \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasFreeShipping) {
            include __DIR__ . '/view/shipping-method-java-script-free-shipping.php';
        }
        /** @TODO: move to custom field & field footer. */
        if ($this instanceof \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasHandlingFees) {
            $price_adjustment_type_field = $settings_prefix . '_' . \DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees::OPTION_PRICE_ADJUSTMENT_TYPE;
            $price_adjustment_value_field = $settings_prefix . '_' . \DhlVendor\WPDesk\WooCommerceShipping\CustomFields\FieldHandlingFees::OPTION_PRICE_ADJUSTMENT_VALUE;
            $price_adjustment_type_none = \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustmentNone::ADJUSTMENT_TYPE;
            include __DIR__ . '/view/shipping-method-java-script-handling-fees.php';
        }
    }
}
