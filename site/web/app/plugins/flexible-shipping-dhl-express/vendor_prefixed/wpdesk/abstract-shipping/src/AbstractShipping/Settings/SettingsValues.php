<?php

/**
 * Settings container: SettingsDefinition.
 *
 * @package WPDesk\AbstractShipping\Settings
 */
namespace DhlVendor\WPDesk\AbstractShipping\Settings;

/**
 * Interface for SettingsValuesAsArray class.
 *
 * @package WPDesk\AbstractShipping\Settings
 */
interface SettingsValues
{
    /**
     * Get value.
     *
     * @param string $name Setting name.
     * @param string|null $default Default value.
     *
     * @return mixed
     */
    public function get_value($name, $default = null);
    /**
     * Has value.
     *
     * @param string $name Setting name.
     *
     * @return bool
     */
    public function has_value($name);
}
