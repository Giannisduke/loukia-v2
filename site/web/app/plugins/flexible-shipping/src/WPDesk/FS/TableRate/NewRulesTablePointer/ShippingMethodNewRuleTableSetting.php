<?php
/**
 * Shipping method settings option.
 *
 * @package WPDesk\FS\TableRate\NewRulesTablePointer
 */

namespace WPDesk\FS\TableRate\NewRulesTablePointer;

use WPDesk\FS\TableRate\NewRulesTableBanner\RulesBannerClickedOption;

/**
 * Can add option to shipping method settings.
 */
class ShippingMethodNewRuleTableSetting {

	const TRIGGER = 'new-rules-table';

	const SETTINGS_OPTION = 'enable_new_rules_table';

	const SHIPPING_METHOD_SETTINGS_OPTION = 'woocommerce_flexible_shipping_info_settings';

	const OPTION_NEW_RULES_DISABLED = 'flexible_shipping_new_rules_was_disabled';

	const VALUE_NO  = 'no';
	const VALUE_YES = 'yes';

	/**
	 * @return bool
	 */
	private function should_add_field_to_settings() {
		return ! wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' )
			&& ! wpdesk_is_plugin_active( 'flexible-shipping-locations/flexible-shipping-locations.php' );
	}

	/**
	 * Add fields to settings.
	 *
	 * @param array $settings Settings.
	 *
	 * @return array
	 */
	public function add_fields_to_settings( array $settings ) {
		if ( $this->should_add_field_to_settings() ) {
			$settings[ self::SETTINGS_OPTION ] = array(
				'type'  => 'checkbox',
				'label' => __( 'Enable New Table Interface', 'flexible-shipping' ),
				'title' => __( 'Rules table', 'flexible-shipping' ),
			);
		}
		return $settings;
	}

	/**
	 * Is new rules table enabled?
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$settings = get_option( self::SHIPPING_METHOD_SETTINGS_OPTION, array() );
		return isset( $settings[ self::SETTINGS_OPTION ] ) && self::VALUE_YES === $settings[ self::SETTINGS_OPTION ];
	}

	/**
	 * Adds action to watch option update.
	 */
	public function watch_settings_change() {
		add_action( 'update_option_' . self::SHIPPING_METHOD_SETTINGS_OPTION, array( $this, 'update_option_if_settings_changed' ), 10, 3 );
	}

	/**
	 * .
	 *
	 * @param array  $old_value .
	 * @param array  $new_value .
	 * @param string $option .
	 */
	public function update_option_if_settings_changed( $old_value, $new_value, $option ) {
		$old_option_value = self::VALUE_NO;
		$new_option_value = self::VALUE_NO;
		if ( is_array( $old_value ) && is_array( $new_value ) ) {
			if ( isset( $old_value[ self::SETTINGS_OPTION ] ) ) {
				$old_option_value = $old_value[ self::SETTINGS_OPTION ];
			}
			if ( isset( $new_value[ self::SETTINGS_OPTION ] ) ) {
				$new_option_value = $new_value[ self::SETTINGS_OPTION ];
			}
		}
		if ( self::VALUE_YES === $old_option_value && self::VALUE_NO === $new_option_value ) {
			update_option( self::OPTION_NEW_RULES_DISABLED, '1' );
		}
	}

	/**
	 * Display settings JavaScript.
	 */
	public function settings_script() {
		return;
		if ( ! RulesPointerOption::is_option_set() ) {
			return;
		}

		$trigger = self::TRIGGER;
		include __DIR__ . '/views/html-flexible-shipping-settings-script.php';
	}

	/**
	 * Checks if pointer was turned off.
	 *
	 * @return bool Option status.
	 */
	public static function was_option_disabled() {
		return 1 === intval( get_option( self::OPTION_NEW_RULES_DISABLED, '0' ) );
	}

	/**
	 * Enable option.
	 */
	public static function enable_option() {
		$shipping_method_settings                          = get_option( self::SHIPPING_METHOD_SETTINGS_OPTION, array() );
		$shipping_method_settings[ self::SETTINGS_OPTION ] = 'yes';
		update_option( self::SHIPPING_METHOD_SETTINGS_OPTION, $shipping_method_settings );
	}

	/**
	 * Enable option.
	 */
	public static function disable_option() {
		$shipping_method_settings                          = get_option( self::SHIPPING_METHOD_SETTINGS_OPTION, array() );
		$shipping_method_settings[ self::SETTINGS_OPTION ] = 'no';
		update_option( self::SHIPPING_METHOD_SETTINGS_OPTION, $shipping_method_settings );
	}

}
