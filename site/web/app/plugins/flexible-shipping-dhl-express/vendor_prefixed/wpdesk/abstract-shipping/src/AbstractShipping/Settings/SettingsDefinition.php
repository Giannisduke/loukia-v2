<?php

/**
 * Settings container: SettingsDefinition.
 *
 * @package WPDesk\AbstractShipping\Settings
 */
namespace DhlVendor\WPDesk\AbstractShipping\Settings;

/**
 * Abstract class for create default settings data.
 *
 * @package WPDesk\AbstractShipping\Settings
 */
abstract class SettingsDefinition
{
    /**
     * Validate settings.
     *
     * @param SettingsValues $settings Settings values.
     *
     * @return bool
     */
    public abstract function validate_settings(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings);
    /**
     * Get settings.
     *
     * @return array
     */
    public abstract function get_form_fields();
}
