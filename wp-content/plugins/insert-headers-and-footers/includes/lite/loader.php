<?php
/**
 * File used for importing lite-only files.
 *
 * @package WPCode
 */

if ( is_admin() || defined( 'DOING_CRON' ) && DOING_CRON ) {
	// Class used for loading the scripts metabox.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/class-wpcode-metabox-snippets-lite.php';
	// Load lite-specific scripts.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/admin-scripts.php';
	// Load lite notices.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/admin/notices.php';
}

add_action( 'plugins_loaded', 'wpcode_plugins_loaded_load_lite_files', 2 );

/**
 * Require files on plugins_loaded.
 *
 * @return void
 */
function wpcode_plugins_loaded_load_lite_files() {
	// Load WooCommerce auto-insert locations.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/auto-insert/class-wpcode-auto-insert-woocommerce.php';
	// Load EDD auto-insert locations.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/auto-insert/class-wpcode-auto-insert-edd.php';
	// Load WooCommerce conditional logic.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/conditional-logic/class-wpcode-conditional-woocommerce.php';
	// Load EDD conditional logic.
	require_once WPCODE_PLUGIN_PATH . 'includes/lite/conditional-logic/class-wpcode-conditional-edd.php';
}
