<?php
/**
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate;

use WPDesk\FS\TableRate\Rule\Condition\ConditionsFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleAdditionalCostFieldsFactory;
use WPDesk\FS\TableRate\Rule\CostsFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleAdditionalCostFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleCostFieldsFactory;
use WPDesk\FS\TableRate\Rule\SpecialAction\SpecialActionFieldsFactory;
use WPDesk\FS\TableRate\Rule\SpecialAction\SpecialActionFactory;

/**
 * Class RulesSettings
 */
class RulesSettingsField {

	/**
	 * @var string
	 */
	private $settings_field_id;

	/**
	 * @var string
	 */
	private $settings_field_name;

	/**
	 * @var string
	 */
	private $settings_field_title;

	/**
	 * @var array
	 */
	private $settings;

	/**
	 * RulesSettings constructor.
	 *
	 * @param string $settings_field_id .
	 * @param string $settings_field_name .
	 * @param string $settings_field_title .
	 * @param array  $settings .
	 */
	public function __construct( $settings_field_id, $settings_field_name, $settings_field_title, $settings ) {
		$this->settings_field_id = $settings_field_id;
		$this->settings_field_name = $settings_field_name;
		$this->settings_field_title = $settings_field_title;
		$this->settings = $settings;
	}

	/**
	 * Render settings.
	 *
	 * @return string
	 */
	public function render() {
		ob_start();
		$settings_field_id    = $this->settings_field_id;
		$settings_field_name  = $this->settings_field_name;
		$settings_field_title = $this->settings_field_title;
		$available_conditions = $this->get_available_conditions();
		$rules_settings       = $this->get_normalized_settings( $available_conditions );
		$available_conditions = array_values( $available_conditions );
		$translations         = $this->get_translations();
		$rules_table_settings = $this->get_table_settings();

		$cost_settings_fields   = $this->get_available_cost_settings();
		$additional_cost_fields = $this->get_additional_cost_fields();
		$special_action_fields  = $this->get_special_actions_fields();

		include __DIR__ . '/views/shipping-method-settings-rules.php';

		return ob_get_clean();
	}

	/**
	 * @return array
	 */
	private function get_translations() {
		return array(
			'conditions'                       => __( 'Conditions', 'flexible-shipping' ),
			'costs'                            => __( 'Costs', 'flexible-shipping' ),
			'special_action'                   => __( 'Special action', 'flexible-shipping' ),
			'when'                             => __( 'When', 'flexible-shipping' ),
			'and'                              => __( 'and', 'flexible-shipping' ),
			'add'                              => __( 'Add', 'flexible-shipping' ),
			'delete'                           => __( 'Delete', 'flexible-shipping' ),
			'add_rule'                         => __( 'Add rule', 'flexible-shipping' ),
			'delete_selected_rules'            => __( 'Delete selected rules', 'flexible-shipping' ),
			'duplicate_selected_rules'         => __( 'Duplicate selected rules', 'flexible-shipping' ),
			'condition_beacon_search'          => __( 'When condition', 'flexible-shipping' ),
			'additional_cost_id_beacon_search' => __( 'additional cost per', 'flexible-shipping' ),
			'additional_cost_id_label'         => __( 'additional cost per', 'flexible-shipping' ),
			'enter_more_characters'            => __( 'Enter 3 or more characters', 'flexible-shipping' ),
			'no_option_text'                   => __( 'Value not found', 'flexible-shipping' ),
			'searching'                        => __( 'searching...', 'flexible-shipping' ),
		);
	}

	/**
	 * @return array
	 */
	private function get_available_cost_settings() {
		$rule_costs_fields_factory = new RuleCostFieldsFactory();
		return $rule_costs_fields_factory->get_normalized_cost_fields();
	}

	/**
	 * @return array
	 */
	private function get_additional_cost_fields() {
		$rule_additional_costs_fields_factory = new RuleAdditionalCostFieldsFactory( ( new RuleAdditionalCostFactory() )->get_additional_costs() );
		return $rule_additional_costs_fields_factory->get_normalized_cost_fields();
	}

	/**
	 * @return array
	 */
	private function get_special_actions_fields() {
		$special_actions_fields_factory = new SpecialActionFieldsFactory( ( new SpecialActionFactory() )->get_special_actions() );
		return $special_actions_fields_factory->get_normalized_cost_fields();
	}

	/**
	 * @return array
	 */
	private function get_table_settings() {
		/**
		 * Rules table settings.
		 *
		 * @param array $settings Table settings.
		 *
		 * @return array Table settings.
		 *
		 * Available settings:
		 *     multiple_conditions_available
		 *     multiple_additional_costs_available
		 */
		return apply_filters(
			'flexible_shipping_rules_table_settings',
			array(
				'multiple_conditions_available'       => false,
				'multiple_additional_costs_available' => false,
				'special_actions_available'           => false,
			)
		);
	}

	/**
	 * @return Rule\Condition\Condition[]
	 */
	private function get_available_conditions() {
		return ( new ConditionsFactory() )->get_conditions();
	}

	/**
	 * @param Rule\Condition\Condition[] $available_conditions .
	 *
	 * @return array
	 */
	private function get_normalized_settings( $available_conditions ) {
		$rules_settings = RulesSettingsFactory::create_from_array( $this->settings['default'] );
		return $this->process_select_options_for_conditions( $rules_settings->get_normalized_settings(), $available_conditions );
	}

	/**
	 * @param array                      $settings .
	 * @param Rule\Condition\Condition[] $available_conditions .
	 */
	private function process_select_options_for_conditions( array $settings, $available_conditions ) {
		foreach ( $settings as $rule_key => $rule ) {
			$conditions = isset( $rule['conditions'] ) && is_array( $rule['conditions'] ) ? $rule['conditions'] : array();
			foreach ( $conditions as $condition_key => $condition ) {
				if ( isset( $available_conditions[ $condition['condition_id'] ] ) ) {
					$settings[ $rule_key ]['conditions'][ $condition_key ] = $available_conditions[ $condition['condition_id'] ]->prepare_settings( $condition );
				}
			}
		}

		return $settings;
	}

}
