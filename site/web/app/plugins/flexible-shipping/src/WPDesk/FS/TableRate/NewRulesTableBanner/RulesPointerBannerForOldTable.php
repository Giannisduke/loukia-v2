<?php
/**
 * Rules pointer banner.
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

namespace WPDesk\FS\TableRate\NewRulesTableBanner;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\Pointer\PointerConditions;
use FSVendor\WPDesk\Pointer\PointerMessage;
use FSVendor\WPDesk\Pointer\PointerPosition;
use FSVendor\WPDesk\Pointer\PointersScripts;
use FSVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use FSVendor\WPDesk\View\Resolver\DirResolver;
use WPDesk\FS\Helpers\ShippingMethod;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;

/**
 * Can display new rules pointer banner.
 */
class RulesPointerBannerForOldTable extends RulesPointerBanner {

	/**
	 * @return string
	 */
	protected function get_banner_file() {
		return __DIR__ . '/views/html-new-rules-table-banner.php';
	}

	/**
	 * Should show pointer.
	 *
	 * @return bool
	 */
	protected function should_show_banner() {
		$new_rule_table_setting = new ShippingMethodNewRuleTableSetting();
		if ( ! isset( $_GET['method_id'] )
			|| $new_rule_table_setting->is_enabled()
			|| RulesBannerLikeOption::is_option_set()
			|| RulesBannerDontLikeOption::is_option_set()
			|| ! ( new \FSVendor\WPDesk_Tracker_Persistence_Consent() )->is_active()
			|| wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' )
			|| ! ShippingMethod::check_if_method_exists_in_zones( 'flexible_shipping' )
			|| isset( $_GET[ self::NEW_RULES_TABLE_PARAMETER ] )
		) {
			return false;
		}

		return true;
	}

}
