<?php
/**
 * Popup clicked option.
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

namespace WPDesk\FS\TableRate\NewRulesTablePopup;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;

/**
 * Can update option when popup is clicked.
 */
class NewRulesPopupClickedOption {

	const OPTION_NAME = 'flexible_shipping_new_rules_popup_clicked';

	/**
	 * Update option.
	 *
	 * @param string $option_value .
	 */
	public function update_option( $option_value ) {
		update_option( self::OPTION_NAME, $option_value );
	}

	/**
	 * Get option value.
	 *
	 * @return string|false
	 */
	public function get_option_value() {
		return get_option( self::OPTION_NAME );
	}

	/**
	 * Checks if option is set.
	 *
	 * @return bool Option status.
	 */
	public static function is_option_set() {
		return false !== get_option( self::OPTION_NAME, false );
	}

}
