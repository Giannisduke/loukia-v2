<?php
/**
 * Rules banner like option.
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

namespace WPDesk\FS\TableRate\NewRulesTableBanner;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;

/**
 * Can update option when banner like is clicked.
 */
class RulesBannerLikeOption implements Hookable {

	const OPTION_NAME = 'flexible_shipping_new_rules_like';

	const AJAX_ACTION = 'flexible_shipping_new_rules_like';

	const NONCE_PARAMETER = 'security';

	const SHIPPING_METHOD_SETTINGS_OPTION = 'woocommerce_flexible_shipping_info_settings';

	const PRIORITY_FIRST = 1;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'wp_ajax_' . static::AJAX_ACTION, array( $this, 'handle_ajax_request' ) );
	}

	/**
	 * Handle AJAX request.
	 *
	 * @internal
	 */
	public function handle_ajax_request() {
		check_ajax_referer( static::AJAX_ACTION, self::NONCE_PARAMETER );
		update_option( static::OPTION_NAME, '1' );
	}

	/**
	 * Checks if pointer is active.
	 *
	 * @return bool Option status.
	 */
	public static function is_option_set() {
		return 1 === intval( get_option( static::OPTION_NAME, '0' ) );
	}

}
