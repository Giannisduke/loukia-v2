<?php
/**
 * Rules pointer banner for new rules table.
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
 * Can display new rules pointer banner on new rules table.
 */
class RulesPointerBannerForNewTable extends RulesPointerBanner {

	/**
	 * @return string
	 */
	protected function get_banner_file() {
		return __DIR__ . '/views/html-new-rules-table-banner-for-new-table.php';
	}

	/**
	 * Should show pointer.
	 */
	protected function should_show_banner() {
		$new_rule_table_setting = new ShippingMethodNewRuleTableSetting();
		if ( $new_rule_table_setting->is_enabled() && ! RulesBannerLikeOption::is_option_set() && ! RulesBannerDontLikeOption::is_option_set()
			&& ! wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' )
			&& ! wpdesk_is_plugin_active( 'flexible-shipping-locations/flexible-shipping-locations.php' )
		) {
			return true;
		}

		return false;
	}


}
