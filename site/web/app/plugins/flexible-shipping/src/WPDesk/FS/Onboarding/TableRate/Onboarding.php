<?php
/**
 * Assets.
 *
 * @package WPDesk\FS\Onboarding
 */

namespace WPDesk\FS\Onboarding\TableRate;

use Flexible_Shipping_Plugin;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Shipping_Zones;

/**
 * Onboarding hooks.
 */
class Onboarding implements Hookable {
	/**
	 * @var FinishOption .
	 */
	private $finish_option;

	/**
	 * @var string .
	 */
	private $scripts_version;

	/**
	 * @var string .
	 */
	private $plugin_assets_url;

	/**
	 * Onboarding constructor.
	 *
	 * @param FinishOption $finish_option     .
	 * @param string       $scripts_version   .
	 * @param string       $plugin_assets_url .
	 */
	public function __construct( FinishOption $finish_option, $scripts_version, $plugin_assets_url ) {
		$this->finish_option     = $finish_option;
		$this->scripts_version   = $scripts_version;
		$this->plugin_assets_url = $plugin_assets_url;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible-shipping/admin/enqueue_scripts', array( $this, 'register_scripts' ), 10, 2 );

		add_action(
			'flexible-shipping/method-rules-settings/table/before',
			array(
				$this,
				'add_onboarding_container',
			)
		);
	}

	/**
	 * Add onboarding container.
	 */
	public function add_onboarding_container() {
		include wp_normalize_path( __DIR__ . '/views/before-table-method-rules-settings.php' );
	}

	/**
	 * @param Flexible_Shipping_Plugin $plugin .
	 * @param string                   $suffix .
	 */
	public function register_scripts( Flexible_Shipping_Plugin $plugin, $suffix ) {
		if ( ! $this->should_load_onboarding() ) {
			return;
		}

		wp_enqueue_style(
			'wpdesk_onboarding',
			sprintf( '%scss/onboarding.css', $this->plugin_assets_url ),
			array(),
			$this->scripts_version
		);

		wp_enqueue_script(
			'wpdesk_onboarding',
			sprintf( '%sjs/onboarding.js', $this->plugin_assets_url ),
			array( 'jquery' ),
			$this->scripts_version,
			true
		);

		wp_localize_script(
			'wpdesk_onboarding',
			'fs_onboarding_details',
			array(
				'ajax'       => array(
					'url'    => admin_url( 'admin-ajax.php' ),
					'nonce'  => wp_create_nonce( OptionAjaxUpdater::NONCE_ACTION ),
					'action' => array(
						'event'           => OptionAjaxUpdater::AJAX_ACTION_EVENT,
						'click'           => OptionAjaxUpdater::AJAX_ACTION_CLICK,
						'auto_show_popup' => OptionAjaxUpdater::AJAX_ACTION_AUTO_SHOP_POPUP,
					),
				),
				'assets_url' => untrailingslashit( $this->plugin_assets_url ),
				'label_step' => __( 'Step #', 'flexible-shipping' ),
				'logo_img'   => 'logo-fs.svg',
				'steps'      => 4,
				'locale'     => get_user_locale(),
				'open_auto'  => $this->should_auto_load(),
				'popups'     => ( new PopupData() )->get_popups(),
			)
		);
	}

	/**
	 * @return bool
	 */
	private function should_auto_load() {
		if ( $this->finish_option->is_option_set() ) {
			return false;
		}

		if ( $this->has_fs_methods() ) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function has_fs_methods() {
		$all_shipping_methods = flexible_shipping_get_all_shipping_methods();
		$flexible_shipping    = $all_shipping_methods['flexible_shipping'];

		$flexible_shipping_rates = $flexible_shipping->get_all_rates();

		return ! empty( $flexible_shipping_rates );
	}

	/**
	 * @return bool
	 */
	private function should_load_onboarding() {
		$tab  = filter_input( INPUT_GET, 'tab' );
		$page = filter_input( INPUT_GET, 'page' );

		if ( 'wc-settings' !== $page || 'shipping' !== $tab ) {
			return false;
		}

		$instance_id = absint( wp_unslash( filter_input( INPUT_GET, 'instance_id' ) ) );

		if ( ! $instance_id ) {
			return false;
		}

		$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );

		if ( ! $shipping_method ) {
			return false;
		}

		if ( ! is_a( $shipping_method, 'WPDesk_Flexible_Shipping' ) ) {
			return false;
		}

		return true;
	}
}
