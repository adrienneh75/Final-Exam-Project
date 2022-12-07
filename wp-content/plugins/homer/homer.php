<?php
/**
 * Homer.
 *
 * @package           iori\homer
 *
 * Plugin Name:       Homer - Formatting Tools
 * Plugin URI:        https://iorilab.com/homer
 * Description:       Add Typewriter/Text change animations, show more, speacial characters and other effects via block formatting toolbar
 * Version:           2.0.2
 * Author:            Iori Lab
 * Author URI:        https://iorilab.com/
 * Text Domain:       homer
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Copyright (C) 2021 contact@iorilab.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace iori\homer;

// Abort if this file is called directly.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Constants.
define( 'IORI_HOMER_ROOT', __FILE__ );
define( 'IORI_HOMER_PREFIX', 'homer' );
define( 'IORI_HOMER__FILE', plugin_basename(__FILE__) );
define( 'IORI_HOMER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The main loader for this plugin
 */
class Main {

	/**
	 * Dependancies
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	public $dependencies;
	public $editorDependencies;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/**
		* Dependencies
		*
		* In order to use Gutenberg, every time you load a library (eg wp.element, wp.blocks)
		* you need to ensure you have added the dependency to the enqueue. Otherwise your
		* application will error (especailly if its the only block in the soloution).
		*/
		$this->editorDependencies = [
			'wp-api',
			'wp-blocks',
			'wp-components',
			'wp-data',
			'wp-editor',
			'wp-element',
			'wp-i18n',
		];

		$this->dependencies = [
			'jquery-core',
		];
	}

	/**
	 * Run all of the plugin functions.
	 *
	 * @since 1.0.0
	 */
	public function run() {

		/**
		 * Load Text Domain
		 */
		load_plugin_textdomain( 'homer', false, IORI_HOMER_ROOT . '\languages' );

		/**
		 * Load assets.
		 */

		// Load Front End Assets.
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 10 );

		// Load Block Editor Assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );

		/**
		 * Load Classes
		 *
		 * Load all the other classes that this plugin needs to run.
		 */
		$this->includes();
	}

	/**
	 * Frontend Assets.
	 *
	 * Assets that load on on the Front End of WordPress.
	 *
	 * @since 1.0.0
	 */
	public function assets() {

		$homer_hidden_text_spoiler_enabled = get_option('homer_hidden_text_spoiler', true);
		$homer_text_animation_enabled = get_option('homer_text_animation', true);
		$homer_uppercase_enabled = get_option('homer_uppercase', true);
		$homer_lowercase_enabled = get_option('homer_lowercase', true);
		$homer_underline_enabled = get_option('homer_underline', true);

		if ($homer_hidden_text_spoiler_enabled OR $homer_text_animation_enabled OR $homer_uppercase_enabled OR $homer_lowercase_enabled OR $homer_underline_enabled){
				$this->include_feature_assets($homer_text_animation_enabled, 'typewriter', true);
				$this->include_feature_assets($homer_hidden_text_spoiler_enabled, 'spoiler', true);
				$this->include_feature_assets($homer_underline_enabled, 'underline');
				$this->include_feature_assets($homer_uppercase_enabled, 'uppercase');
				$this->include_feature_assets($homer_lowercase_enabled, 'lowercase');
        }

	}


	/**
	 * Features Assets.
	 *
	 * Assets that load on on the Front End of WordPress.
	 * Including only if feature is present on the page
	 *
	 * @since 2.0
	 */
	public function include_feature_assets($enabled, $feature, $js = false) {

		if ( $enabled ) {
	
			if ( !is_admin() && $js ) {
				// Enqueue JS.
				wp_enqueue_script(
					'homer-'.$feature,
					plugins_url( '/assets/js/frontend/'.$feature.'FrontEnd.js', __FILE__ ),
					$this->dependencies,
					filemtime( plugin_dir_path( __FILE__ ) . '/assets/js/frontend/'.$feature.'FrontEnd.js' ),
					true
				);
			}
	
			// Enqueue Styles.
			wp_enqueue_style(
				'homer-'.$feature,
				plugins_url( '/assets/css/'.$feature.'.css', __FILE__ ),
				[],
				filemtime( plugin_dir_path( __FILE__ ) . '/assets/css/'.$feature.'.css' )
			);
		}

	}

	/**
	 * Block Editor Assets.
	 *
	 * Assets that load on on the Gutenberg 'Edit' interface. Use the styles to
	 * add styles that only impact the 'edit' view.
	 *
	 * The `editor.js` file is the combined React for your Gutenberg Block
	 *
	 * @since 1.0.0
	 */
	public function editor_assets() {

		// check if feature is enabled
		$homer_hidden_text_spoiler_enabled = get_option('homer_hidden_text_spoiler', true);
		$homer_text_animation_enabled = get_option('homer_text_animation', true);
		$homer_uppercase_enabled = get_option('homer_uppercase', true);
		$homer_lowercase_enabled = get_option('homer_lowercase', true);
		$homer_underline_enabled = get_option('homer_underline', true);
		$homer_special_characters = get_option('homer_special_characters', true);

		// include feature in editor
		$this->include_editor_assets(true, 'editor', 'editor');
		
		if ($homer_hidden_text_spoiler_enabled OR $homer_text_animation_enabled OR $homer_uppercase_enabled OR $homer_lowercase_enabled OR $homer_underline_enabled OR $homer_special_characters){
			$this->include_editor_assets($homer_hidden_text_spoiler_enabled, 'spoiler', 'editor/spoilerEditor');
			$this->include_editor_assets($homer_text_animation_enabled, 'typewriter', 'editor/typewriterEditor');
			$this->include_editor_assets($homer_uppercase_enabled, 'uppercase', 'uppercase');
			$this->include_editor_assets($homer_lowercase_enabled, 'lowercase', 'lowercase');
			$this->include_editor_assets($homer_underline_enabled, 'underline', 'underline');
			$this->include_editor_assets($homer_special_characters, 'specialChars', 'editor/specialChars');
		}

	}

	public function include_editor_assets($enabled, $featureName, $style) {

		if ( $enabled ) {

			// Enqueue editor JS.
			wp_enqueue_script(
			'homer-editor-'.$featureName,
			plugins_url( '/assets/js/editor/'.$featureName.'.js', __FILE__ ),
			$this->editorDependencies,
			filemtime( plugin_dir_path( __FILE__ ) . '/assets/js/editor/'.$featureName.'.js' ),
			true
			);

			// Enqueue edtior Styles.
			wp_enqueue_style(
			'homer-editor-'.$featureName,
			plugins_url( '/assets/css/'.$style.'.css', __FILE__ ),
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . '/assets/css/'.$style.'.css' )
			);

		}

	}

	/**
	 * Include Classes
	 */
	public function includes() {

		// Require Classes.
		require_once 'lib/class-activator.php';
		require_once 'lib/class-deactivator.php';
		require_once 'lib/class-uninstaller.php';
		require_once 'lib/settingsPage.php';
		require_once 'lib/plugin-action-meta-links.php';

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			require_once 'lib/class-user-feedback.php';
		}

		// Instantiate Classes.
		$activator       = new Activator();
		$deactivator     = new Deactivator();
		$uninstaller     = new Uninstaller();
		$settingspage	 = new SettingsPage();
		$actionMetaLinks = new ActionMetaLinks();

		// Run Code.
		$activator->run();        // Run code on activation.
		$deactivator->run();      // Run code on deactivation.
		$uninstaller->run();      // Run code on uninstallation.
		$settingspage->run();	  // Run code to setup settings page.
		$actionMetaLinks->run();  // Run code to create action and row meta links
	}
}

$main = new Main();
$main->run();
