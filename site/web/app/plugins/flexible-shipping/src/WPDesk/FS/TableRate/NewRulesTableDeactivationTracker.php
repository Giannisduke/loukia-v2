<?php
/**
 * Decativation tracker data.
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
 * Can add new rates table data to deactivation tracker data.
 */
class NewRulesTableDeactivationTracker implements Hookable {

	const ADDITIONAL_DATA = 'additional_data';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_deactivation_data', array( $this, 'append_new_rules_table_data_to_deactivation_tracker' ) );
	}

	/**
	 * Set new rules table data to data array.
	 *
	 * @param array $data Data.
	 *
	 * @return array
	 */
	public function append_new_rules_table_data_to_deactivation_tracker( array $data ) {
		if ( empty( $data[ self::ADDITIONAL_DATA ] ) || ! is_array( $data[ self::ADDITIONAL_DATA ] ) ) {
			$data[ self::ADDITIONAL_DATA ] = array();
		}
		$data[ self::ADDITIONAL_DATA ]['fs_new_rules_table_v4'] = ( new NewRulesTableTrackerData() )->get_data();

		return $data;
	}

}
