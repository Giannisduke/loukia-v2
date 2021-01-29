<?php
/**
 * HelpScout Beacon.
 *
 * @package WPDesk\FlexibleShippingDhl
 */

namespace WPDesk\FlexibleShippingDhl;

use DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can display HelpScout Beacon.
 */
class Beacon implements Hookable {

	/**
	 * .
	 *
	 * @var string
	 */
	private $beacon_id;

	/**
	 * .
	 *
	 * @var array
	 */
	private $beacon_location_parameters;

	/**
	 * .
	 *
	 * @var string
	 */
	private $assets_url;

	/**
	 * Beacon constructor.
	 *
	 * @param string $beacon_id .
	 * @param array  $beacon_location_parameters .
	 * @param string $assets_url .
	 */
	public function __construct( $beacon_id, $beacon_location_parameters, $assets_url ) {
		$this->beacon_id = $beacon_id;
		$this->beacon_location_parameters = $beacon_location_parameters;
		$this->assets_url = $assets_url;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'admin_footer', array( $this, 'add_beacon_to_footer' ) );
	}

	/**
	 * Should display beacon?
	 *
	 * @return bool
	 */
	private function should_display_beacon() {
		$display = true;
		foreach ( $this->beacon_location_parameters as $parameter => $value ) {
			if ( ! isset( $_GET[ $parameter ] ) || $_GET[ $parameter ] !== $value ) {
				$display = false;
			}
		}
		return $display;
	}

	/**
	 * Display Beacon script.
	 */
	public function add_beacon_to_footer() {
		if ( $this->should_display_beacon() ) {
			$beacon_id = $this->beacon_id;
			$button_image_src = $this->assets_url . 'images/beacon-better-transparent.png';
			include __DIR__ . '/views/html-beacon-script.php';
		}
	}

}
