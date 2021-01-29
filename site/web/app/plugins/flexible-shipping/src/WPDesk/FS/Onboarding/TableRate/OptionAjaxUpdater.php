<?php
/**
 * Onboarding option AJAX updater.
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

namespace WPDesk\FS\Onboarding\TableRate;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can update option when onboarding is clicked.
 */
class OptionAjaxUpdater implements Hookable {
	const AJAX_ACTION_CLICK = 'flexible_shipping_onboarding_table_rate_click';
	const AJAX_ACTION_EVENT = 'flexible_shipping_onboarding_table_rate_event';
	const AJAX_ACTION_AUTO_SHOP_POPUP = 'flexible_shipping_onboarding_table_rate_auto_show_popup';

	const NONCE_ACTION = 'flexible_shipping_onboarding_table_rate';

	/**
	 * @var FinishOption
	 */
	private $option;

	/**
	 * OptionAjaxUpdater constructor.
	 *
	 * @param FinishOption $option .
	 */
	public function __construct( $option ) {
		$this->option = $option;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION_CLICK, array( $this, 'handle_ajax_action_click' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_EVENT, array( $this, 'handle_ajax_action_event' ) );
		add_action(
			'wp_ajax_' . self::AJAX_ACTION_AUTO_SHOP_POPUP,
			array(
				$this,
				'handle_ajax_action_auto_show_popup',
			)
		);
	}

	/**
	 * Handle AJAX action OK.
	 *
	 * @internal
	 */
	public function handle_ajax_action_event() {
		check_ajax_referer( self::NONCE_ACTION );

		$event = filter_input( INPUT_POST, 'event' );

		if ( ! $event ) {
			wp_send_json_error();
		}

		$this->option->update_option( 'event', sanitize_text_field( $event ) );

		wp_send_json_success();
	}

	/**
	 * Handle AJAX action Click.
	 *
	 * @internal
	 */
	public function handle_ajax_action_click() {
		check_ajax_referer( self::NONCE_ACTION );

		$clicks = (int) $this->option->get_option_value( 'clicks' );

		$this->option->update_option( 'clicks', ++ $clicks );

		wp_send_json_success();
	}

	/**
	 * Handle AJAX action Click.
	 *
	 * @internal
	 */
	public function handle_ajax_action_auto_show_popup() {
		check_ajax_referer( self::NONCE_ACTION );

		$this->option->update_option( 'auto_show_popup', true );

		wp_send_json_success();
	}
}
