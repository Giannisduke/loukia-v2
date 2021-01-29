<?php
/**
 * Popup clicked option AJAX updater.
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

namespace WPDesk\FS\TableRate\NewRulesTablePopup;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\NewRulesTablePointer\ShippingMethodNewRuleTableSetting;

/**
 * Can update option when popup is clicked.
 */
class NewRulesPopupClickedOptionAjaxUpdater implements Hookable {

	const NONCE_ACTION = 'flexible_shipping_nrt_popup_clicked';
	const AJAX_ACTION_OK  = 'flexible_shipping_nrt_popup_ok_clicked';
	const AJAX_ACTION_CANCEL  = 'flexible_shipping_nrt_popup_cancel_clicked';

	/**
	 * @var NewRulesPopupClickedOption
	 */
	private $option;

	/**
	 * NewRulesPopupClickedOptionAjaxUpdater constructor.
	 *
	 * @param NewRulesPopupClickedOption $option .
	 */
	public function __construct( NewRulesPopupClickedOption $option ) {
		$this->option = $option;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION_OK, array( $this, 'handle_ajax_action_ok' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_CANCEL, array( $this, 'handle_ajax_action_cancel' ) );
	}

	/**
	 * Handle AJAX action OK.
	 *
	 * @internal
	 */
	public function handle_ajax_action_ok() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );
		$this->option->update_option( 'ok' );
		ShippingMethodNewRuleTableSetting::enable_option();
	}

	/**
	 * Handle AJAX action OK.
	 *
	 * @internal
	 */
	public function handle_ajax_action_cancel() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );
		$this->option->update_option( 'cancel' );
	}

}
