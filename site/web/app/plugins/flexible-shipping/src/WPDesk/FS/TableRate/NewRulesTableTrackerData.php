<?php
/**
 * New Rules Table tracker data.
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate;

use WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerClickedOption;
use WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerDontLikeOption;
use WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerLikeOption;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;
use WPDesk\FS\TableRate\NewRulesTablePopup\NewRulesPopupClickedOption;

/**
 * Class NewRulesTableTrackerData
 */
class NewRulesTableTrackerData {

	const NEW_USERS_AFTER_THIS_DATE = '2020-12-01 01:00:00';

	/**
	 * If this a old user? If so then FS should work like always.
	 *
	 * @return bool
	 */
	private function is_new_installation() {
		return strtotime( self::NEW_USERS_AFTER_THIS_DATE ) < $this->activation_date_according_to_wpdesk_helper();
	}

	/**
	 * Activation date according to wpdesk helper.
	 *
	 * @return int Activation date timestamp.
	 */
	private function activation_date_according_to_wpdesk_helper() {
		$option_name     = 'plugin_activation_flexible-shipping/flexible-shipping.php';
		$activation_date = get_option( $option_name, current_time( 'mysql' ) );

		if ( ! $activation_date ) {
			return time();
		}

		return strtotime( $activation_date );
	}

	/**
	 * Checks if new tables are activated.
	 *
	 * @return string Status "yes" for true or "no" for false.
	 */
	private function get_new_rules_table_enabled() {
		$shipping_method_settings = get_option( ShippingMethodNewRuleTableSetting::SHIPPING_METHOD_SETTINGS_OPTION, array() );
		if ( isset( $shipping_method_settings[ ShippingMethodNewRuleTableSetting::SETTINGS_OPTION ] ) ) {
			return $shipping_method_settings[ ShippingMethodNewRuleTableSetting::SETTINGS_OPTION ];
		}
		return 'no';
	}

	/**
	 * @return array
	 */
	public function get_data() {
		$new_rules_table_data = array();

		$new_rules_table_data['new_installation']  = $this->is_new_installation() ? 'yes' : 'no';
		$new_rules_table_data['table_enabled']     = $this->get_new_rules_table_enabled();
		$new_rules_table_data['popup_clicked']     = $this->get_popup_clicked();
		$new_rules_table_data['like_clicked']      = RulesBannerLikeOption::is_option_set() ? 'yes' : 'no';
		$new_rules_table_data['dont_like_clicked'] = RulesBannerDontLikeOption::is_option_set() ? 'yes' : 'no';
		$new_rules_table_data['was_disabled']      = ShippingMethodNewRuleTableSetting::was_option_disabled() ? 'yes' : 'no';

		$user_feedback = get_option( UserFeedback::USER_FEEDBACK_OPTION );

		if ( is_array( $user_feedback ) ) {
			$new_rules_table_data = $this->append_user_feedback_data( $new_rules_table_data, $user_feedback );
		}
		return $new_rules_table_data;
	}

	/**
	 * @return string
	 */
	private function get_popup_clicked() {
		$popup_clicked_option = new NewRulesPopupClickedOption();
		return $popup_clicked_option->get_option_value() ? $popup_clicked_option->get_option_value() : 'no';
	}

	/**
	 * Adds info about New Table Interface to user feedback.
	 *
	 * @param array $data Stats about New Table Interface.
	 * @param array $user_feedback User feedback.
	 *
	 * @return array Updated $data array.
	 */
	private function append_user_feedback_data( $data, $user_feedback ) {
		if ( isset( $user_feedback['selected_option'] ) ) {
			$data['feedback_option'] = $user_feedback['selected_option'];
		}
		if ( isset( $user_feedback['additional_info'] ) ) {
			$data['feedback_additional_info'] = $user_feedback['additional_info'];
		}

		return $data;
	}

}
