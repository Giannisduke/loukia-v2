<?php
/**
 * Rules pointer banner.
 *
 * @package WPDesk\FS\TableRate\NewRulesTableBanner
 */

namespace WPDesk\FS\TableRate\NewRulesTableBanner;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\Pointer\PointerConditions;
use FSVendor\WPDesk\Pointer\PointerMessage;
use FSVendor\WPDesk\Pointer\PointerPosition;
use FSVendor\WPDesk\Pointer\PointersScripts;
use FSVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use FSVendor\WPDesk\View\Resolver\DirResolver;
use WPDesk\FS\Helpers\ShippingMethod;

/**
 * Can display new rules pointer banner.
 */
abstract class RulesPointerBanner implements Hookable {

	const NEW_RULES_TABLE_PARAMETER = 'new_rules_table';

	const TRIGGER = 'new-rules-table-feedback';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible_shipping_method_script', array( $this, 'add_new_rules_banner_script' ) );
	}

	/**
	 * Should show banner?
	 *
	 * @return bool
	 */
	abstract protected function should_show_banner();

	/**
	 * Get banner file.
	 *
	 * @return string
	 */
	abstract protected function get_banner_file();

	/**
	 * Add pointer messages if it should be visible.
	 *
	 * @internal
	 */
	public function add_new_rules_banner_script() {
		if ( ! $this->should_show_banner() ) {
			return;
		}

		$this->add_script();
	}

	/**
	 * Creates pointer message.
	 */
	private function add_script() {
		$banner_file = $this->get_banner_file();
		$trigger = self::TRIGGER;
		include __DIR__ . '/views/html-new-rules-table-script.php';
	}

}
