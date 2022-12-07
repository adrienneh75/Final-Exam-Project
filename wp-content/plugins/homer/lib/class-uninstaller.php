<?php
/**
 * Uninstaller
 *
 * @since 1.0.0
 *
 * @package iori\homer
 */

namespace iori\homer;

// Abort if this file is called directly.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Uninstaller.
 *
 * Runs when the plugin is uninstalled.
 */
class Uninstaller {

	/**
	 * Run Code.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		register_uninstall_hook( IORI_HOMER_ROOT, array( 'iori\homer\Uninstaller', 'uninstall' ) );
	}

	/**
	 * Uninstall
	 *
	 * Runs code on uninstall.
	 *
	 * @since 1.0.0
	 */
	public static function uninstall() {
		// Remove a transient to confirm uninstallation.
		delete_transient( IORI_HOMER_PREFIX . '_activated' );
	}
}
