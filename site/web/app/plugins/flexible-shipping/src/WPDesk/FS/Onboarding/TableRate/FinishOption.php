<?php
/**
 * Onboarding clicked option.
 *
 * @package WPDesk\FS\Onboarding
 */

namespace WPDesk\FS\Onboarding\TableRate;

/**
 * Can update option when onboarding is finish.
 */
class FinishOption {
	const OPTION_NAME = 'flexible_shipping_onboarding_table_rate';

	/**
	 * Get option value.
	 *
	 * @param string $key     .
	 * @param bool   $default .
	 *
	 * @return mixed
	 */
	public function get_option_value( $key = '', $default = false ) {
		$options = $this->get_options();

		if ( $key ) {
			return isset( $options[ $key ] ) ? $options[ $key ] : $default;
		}

		return $options;
	}

	/**
	 * Checks if option is set.
	 *
	 * @return bool Option status.
	 */
	public function is_option_set() {
		return false !== get_option( self::OPTION_NAME, false );
	}

	/**
	 * @param string $option_key   .
	 * @param mixed  $option_value .
	 *
	 * @return bool
	 */
	public function update_option( $option_key, $option_value ) {
		$options = $this->get_options();

		$options[ $option_key ] = $option_value;

		return update_option( self::OPTION_NAME, $options );
	}

	/**
	 * @return array
	 */
	private function get_options() {
		$options = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		return wp_parse_args( $options, $this->get_default_option_values() );
	}

	/**
	 * @return array
	 */
	private function get_default_option_values() {
		return array(
			'clicks'          => 0,
			'event'           => 'none',
			'auto_show_popup' => 0,
		);
	}
}
