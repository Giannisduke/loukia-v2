<?php
$installer_path = 'vendor/otgs/installer';
if ( isset( $wp_installer_instance ) && file_exists( WCML_PLUGIN_PATH . '/' . $installer_path . '/loader.php' ) ) {

	include WCML_PLUGIN_PATH . '/' . $installer_path . '/loader.php';
	$args = [
		'plugins_install_tab' => 1,
	];
	WP_Installer_Setup( $wp_installer_instance, $args );
}
