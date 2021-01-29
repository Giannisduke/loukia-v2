<?php
/**
 * Class Plugin Details
 *
 * @package WPDesk\FS\Compatibility
 */

namespace WPDesk\FS\Compatibility;

/**
 * Class PluginDetails
 *
 * @package WPDesk\FS\Compatibility
 */
class PluginDetails {
	/**
	 * @var string .
	 */
	private $plugin_file;

	/**
	 * @var string|null .
	 */
	private $version_constant_name;

	/**
	 * @var string .
	 */
	private $required_version;

	/**
	 * PluginChecker constructor.
	 *
	 * @param string $plugin_file           .
	 * @param string $version_constant_name .
	 * @param string $required_version      .
	 */
	public function __construct( $plugin_file, $version_constant_name, $required_version ) {
		$this->plugin_file           = $plugin_file;
		$this->version_constant_name = $version_constant_name;
		$this->required_version      = $required_version;
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return wpdesk_is_plugin_active( $this->plugin_file );
	}

	/**
	 * @return string
	 */
	public function get_version() {
		return defined( $this->version_constant_name ) ? constant( $this->version_constant_name ) : '0.0.0';
	}

	/**
	 * @return string
	 */
	public function get_required_version() {
		return $this->required_version;
	}

	/**
	 * @return bool
	 */
	public function is_compatible() {
		return $this->compare_version( $this->required_version, '>=' );
	}

	/**
	 * @param string $version  .
	 * @param string $operator .
	 *
	 * @return bool
	 */
	private function compare_version( $version, $operator = null ) {
		return version_compare( $this->get_version(), $version, $operator );
	}
}
