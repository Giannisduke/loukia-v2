<?php
/**
 * Tracker data.
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerClickedOption;
use WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerDontLikeOption;
use WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerLikeOption;
use WPDesk\FS\TableRate\NewRulesTablePointer\RulesPointerOption;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;

/**
 * Can add new rates table data to tracked data.
 */
class NewRulesTableTracker implements Hookable {

	const PRIORITY_AFTER_FS_TRACKER = 12;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_data', array( $this, 'append_new_rules_table_data_to_tracker' ), self::PRIORITY_AFTER_FS_TRACKER );
	}

	/**
	 * Adds data to the tracker about New Table Interface.
	 *
	 * @param array $data Original data sent to tracker.
	 *
	 * @return array Updated $data array.
	 */
	public function append_new_rules_table_data_to_tracker( $data ) {
		$data['flexible_shipping']['new_rules_table_v4'] = ( new NewRulesTableTrackerData() )->get_data();

		return $data;
	}

}
