<?php
/**
 * Rules banner dont like option.
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

namespace WPDesk\FS\TableRate\NewRulesTableBanner;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;

/**
 * Can update option when dont like is clicked.
 */
class RulesBannerDontLikeOption extends RulesBannerLikeOption {

	const OPTION_NAME = 'flexible_shipping_new_rules_dont_like';

	const AJAX_ACTION = 'flexible_shipping_new_rules_dont_like';

	/**
	 * Handle AJAX request.
	 *
	 * @internal
	 */
	public function handle_ajax_request() {
		check_ajax_referer( static::AJAX_ACTION, self::NONCE_PARAMETER );
		$this->update_shipping_method_settings();
		update_option( static::OPTION_NAME, '1' );
	}

	/**
	 * Update shipping method settings.
	 */
	private function update_shipping_method_settings() {
		ShippingMethodNewRuleTableSetting::disable_option();
	}

}
