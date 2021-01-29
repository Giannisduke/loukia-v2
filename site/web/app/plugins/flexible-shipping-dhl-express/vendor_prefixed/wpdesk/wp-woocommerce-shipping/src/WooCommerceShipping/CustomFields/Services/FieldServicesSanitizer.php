<?php

/**
 * Custom fields: FieldServicesSanitizer class.
 *
 * @package WPDesk\WooCommerceShipping\CustomFields
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\CustomFields\Services;

/**
 * Can sanitize services field.
 */
class FieldServicesSanitizer
{
    const FIELD_NAME = 'name';
    const FIELD_ENABLED = 'enabled';
    /**
     * Sanitize services.
     *
     * @param $custom_services
     *
     * @return mixed
     */
    public function sanitize_services($custom_services)
    {
        if (\is_array($custom_services)) {
            foreach ($custom_services as $key => $custom_service) {
                $custom_services[$key] = $this->sanitize_single_service($custom_service);
            }
        }
        return $custom_services;
    }
    /**
     * Sanitize single service.
     *
     * @param array $custom_service .
     *
     * @return array
     */
    private function sanitize_single_service($custom_service)
    {
        if (\is_array($custom_service)) {
            if (isset($custom_service[self::FIELD_NAME])) {
                $custom_service[self::FIELD_NAME] = \sanitize_text_field(\wp_unslash($custom_service[self::FIELD_NAME]));
            }
            if (isset($custom_service[self::FIELD_ENABLED])) {
                $custom_service[self::FIELD_ENABLED] = \sanitize_text_field($custom_service[self::FIELD_ENABLED]);
            }
        }
        return $custom_service;
    }
}
