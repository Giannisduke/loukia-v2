<?php

/**
 * Settings container: SettingsDefinitionModifierBefore.
 *
 * @package WPDesk\AbstractShipping\Settings
 */
namespace DhlVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier;

use DhlVendor\WPDesk\AbstractShipping\Exception\SettingsFieldNotExistsException;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
/**
 * Can modify settings by adding settings field before given field.
 */
class SettingsDefinitionModifierBefore extends \DhlVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition
{
    /**
     * Decorated settings definition.
     *
     * @var SettingsDefinition
     */
    private $decorated_settings_definition;
    /**
     * Field id.
     *
     * @var string
     */
    private $field_id_before;
    /**
     * New field id.
     *
     * @var string
     */
    private $new_field_id;
    /**
     * New field.
     *
     * @var array
     */
    private $new_field;
    /**
     * SettingsDefinitionModifierBefore constructor.
     *
     * @param SettingsDefinition $decorated_settings_definition Decorated settings definition,
     * @param string $field_id_before Field id before which should be settings added.
     * @param string $new_field_id New field id.
     * @param array  $new_field New field.
     */
    public function __construct(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition $decorated_settings_definition, $field_id_before, $new_field_id, array $new_field)
    {
        $this->decorated_settings_definition = $decorated_settings_definition;
        $this->field_id_before = $field_id_before;
        $this->new_field_id = $new_field_id;
        $this->new_field = $new_field;
    }
    /**
     * Returns modified form fields.
     *
     * @param array $form_fields
     *
     * @return array
     *
     * @throws SettingsFieldNotExistsException
     */
    public function get_form_fields()
    {
        $form_fields = $this->decorated_settings_definition->get_form_fields();
        if (isset($form_fields[$this->field_id_before])) {
            $modified_form_fields = [];
            foreach ($form_fields as $field_id => $field) {
                if ($field_id === $this->field_id_before) {
                    $modified_form_fields[$this->new_field_id] = $this->new_field;
                }
                $modified_form_fields[$field_id] = $field;
            }
            return $modified_form_fields;
        }
        throw new \DhlVendor\WPDesk\AbstractShipping\Exception\SettingsFieldNotExistsException(\sprintf('Field %1$s not found in settings!', $this->field_id_before));
    }
    /**
     * Validate settings.
     *
     * @param SettingsValues $settings Settings values.
     *
     * @return bool
     */
    public function validate_settings(\DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings)
    {
        return $this->decorated_settings_definition->validate_settings($settings);
    }
}
