<?php
/**
 * Class Assets
 *
 * @package WPDesk\FS\TableRate\NewRulesTablePopup
 */

namespace WPDesk\FS\TableRate\NewRulesTablePopup;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;

/**
 * Can enqueue assets for new rules table popup.
 */
class Assets implements Hookable {

	/**
	 * @var string
	 */
	private $assets_url;

	/**
	 * @var string
	 */
	private $scripts_version;

	/**
	 * @var ShippingMethodNewRuleTableSetting
	 */
	private $new_rule_table_setting;

	/**
	 * @var NewRulesPopupClickedOption
	 */
	private $option_clicked;

	/**
	 * Assets constructor.
	 *
	 * @param string                            $assets_url .
	 * @param string                            $scripts_version .
	 * @param ShippingMethodNewRuleTableSetting $new_rule_table_setting .
	 * @param NewRulesPopupClickedOption        $option_clicked .
	 */
	public function __construct(
		$assets_url,
		$scripts_version,
		ShippingMethodNewRuleTableSetting $new_rule_table_setting,
		NewRulesPopupClickedOption $option_clicked
	) {
		$this->assets_url = $assets_url;
		$this->scripts_version = $scripts_version;
		$this->new_rule_table_setting = $new_rule_table_setting;
		$this->option_clicked = $option_clicked;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		if ( ! wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' )
			&& ! wpdesk_is_plugin_active( 'flexible-shipping-locations/flexible-shipping-locations.php' )
			&& ! $this->new_rule_table_setting->is_enabled()
			&& ! $this->option_clicked->get_option_value()
			&& ( new \FSVendor\WPDesk_Tracker_Persistence_Consent() )->is_active()
		) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		if ( $this->is_flexible_shipping_method_settings_page() ) {
			$handle = 'fs-nrt-popup';
			wp_register_script(
				$handle,
				trailingslashit( $this->assets_url ) . 'js/new-rules-table-popup.js',
				array(),
				$this->scripts_version
			);
			wp_localize_script(
				$handle,
				'fs_new_rules_table_popup_properties',
				array(
					'ajax_url'           => admin_url( 'admin-ajax.php' ),
					'nonce'              => wp_create_nonce( NewRulesPopupClickedOptionAjaxUpdater::NONCE_ACTION ),
					'assets_url'         => untrailingslashit( $this->assets_url ),
					'ajax_action_ok'     => NewRulesPopupClickedOptionAjaxUpdater::AJAX_ACTION_OK,
					'ajax_action_cancel' => NewRulesPopupClickedOptionAjaxUpdater::AJAX_ACTION_CANCEL,
					// Translators: html tags.
					'title'              => sprintf( __( 'We are introducing a completely %1$snew table interface!%2$s', 'flexible-shipping' ), '<span>', '</span>' ),
					'logo_img'           => 'logo-fs@2x.png',
					'whats_new'          => __( 'What\'s new?', 'flexible-shipping' ),
					'ok_button'          => __( 'Give it a try!', 'flexible-shipping' ),
					'ok_button_url'      => admin_url( 'admin.php?page=wc-settings&tab=shipping&instance_id=' . sanitize_key( isset( $_GET['instance_id'] ) ? $_GET['instance_id'] : '' ) . '&method_id=' . sanitize_key( isset( $_GET['method_id'] ) ? $_GET['method_id'] : '' ) . '&action=edit&' . NewRulesPopupClickedOption::OPTION_NAME . '=1' ),
					// phpcs:ignore
					'cancel_button'      => __( 'Keep the old version', 'flexible-shipping' ),
					'sections'           => array(
						array(
							'image'   => 'design@2x.png',
							'heading' => __( 'Intuitive and clean design', 'flexible-shipping' ),
							'text'    => __( 'Everything kept as simple as possible. All the options just where you need them.', 'flexible-shipping' ),
						),
						array(
							'image'   => 'configuration@2x.png',
							'heading' => __( 'Easier configuration', 'flexible-shipping' ),
							'text'    => __( 'No more confusing setup. Make the shipping fit your needs within a few clicks.', 'flexible-shipping' ),
						),
						array(
							'image'   => 'delivery@2x.png',
							'heading' => __( 'Just set and go!', 'flexible-shipping' ),
							'text'    => __( 'Turn the plugin on, define the shipping cost calculation rules and let it do the rest!', 'flexible-shipping' ),
						),
					),
				)
			);
			wp_enqueue_script( $handle );

			wp_enqueue_style(
				$handle,
				trailingslashit( $this->assets_url ) . 'css/new-rules-table-popup.css',
				array(),
				$this->scripts_version
			);
		}
	}

	/**
	 * @return bool
	 */
	private function is_flexible_shipping_method_settings_page() {
		if ( isset( $_GET['method_id'] ) && isset( $_GET['instance_id'] ) ) { // phpcs:ignore
			$instance_id = sanitize_text_field( $_GET['instance_id'] );  // phpcs:ignore
			try {
				$shipping_method = \WC_Shipping_Zones::get_shipping_method( $instance_id );
				if ( $shipping_method && $shipping_method instanceof \WPDesk_Flexible_Shipping ) {

					return true;
				}
			} catch ( Exception $e ) {

				return false;
			}
		}

		return false;
	}

}
