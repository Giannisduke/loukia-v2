<?php
/**
 * Rules banner clicked option.
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

namespace WPDesk\FS\TableRate\NewRulesTableBanner;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;

/**
 * Can update option when banner is clicked.
 */
class RulesBannerClickedOption implements Hookable {

	const OPTION_NAME = 'flexible_shipping_new_rules_banner_clicked';

	/**
	 * Hooks.
	 */
	public function hooks() {
		if ( current_user_can( 'manage_woocommerce' )
			&& $this->has_parameter_value_in_get( 'page', 'wc-settings' )
			&& $this->has_parameter_value_in_get( 'tab', 'shipping' )
			&& $this->is_parameter_present_in_get( 'instance_id' )
			&& $this->is_parameter_present_in_get( 'method_id' )
			&& $this->is_parameter_present_in_get( 'new_rules_table' )
		) {
			add_action( 'admin_init', array( $this, 'set_option' ) );
		}
	}

	/**
	 * @param string $parameter .
	 *
	 * @return bool
	 */
	private function is_parameter_present_in_get( $parameter ) {
		return isset( $_GET[ $parameter ] );
	}

	/**
	 * @param string $parameter .
	 * @param string $parameter_value .
	 *
	 * @return bool
	 */
	private function has_parameter_value_in_get( $parameter, $parameter_value ) {
		return isset( $_GET[ $parameter ] ) && $_GET[ $parameter ] === $parameter_value;
	}

	/**
	 * Set option.
	 *
	 * @internal
	 */
	public function set_option() {
		update_option( self::OPTION_NAME, '1' );
	}

	/**
	 * Checks if option is set.
	 *
	 * @return bool Option status.
	 */
	public static function is_option_set() {
		return 1 === intval( get_option( self::OPTION_NAME, '0' ) );
	}

}
