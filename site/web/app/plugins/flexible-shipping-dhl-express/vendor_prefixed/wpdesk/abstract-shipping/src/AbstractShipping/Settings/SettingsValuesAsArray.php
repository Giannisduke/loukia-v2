<?php

/**
 * Settings container: SettingsValuesAsArray.
 *
 * @package WPDesk\AbstractShipping\Settings
 */
namespace DhlVendor\WPDesk\AbstractShipping\Settings;

/**
 * Container class for settings data.
 *
 * @package WPDesk\AbstractShipping\Settings
 */
class SettingsValuesAsArray implements \DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues
{
    /**
     * Values.
     *
     * @var array
     */
    private $values;
    /**
     * SettingsValuesAsArray constructor.
     *
     * @param array $values Array values.
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }
    /**
     * Get value.
     *
     * @param string $name Setting name.
     * @param string|null $default Default value if no value found.
     *
     * @return mixed
     */
    public function get_value($name, $default = null)
    {
        return $this->has_value($name) ? $this->values[$name] : $default;
    }
    /**
     * Has value.
     *
     * @param string $name Setting name.
     *
     * @return bool
     */
    public function has_value($name)
    {
        return isset($this->values[$name]);
    }
}
