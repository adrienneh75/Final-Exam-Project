<?php
/**
 * Settings Page
 *
 * @since 2.0.0
 *
 * @package iori\homer
 */

namespace iori\homer;

// Abort if this file is called directly.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
* Setting page
*/
class SettingsPage {
	public function run() {
		add_action( 'admin_menu', array( $this, 'homer_add_option_menu' ) );
		add_action( 'init', array( $this, 'homer_register_settings' ) );
	}

	public function homer_options_assets() {
		wp_enqueue_script( 'iori-homer-script', plugins_url( '../', __FILE__ ) . 'assets/js/settingsPage.js', array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), null, true );
		wp_enqueue_style( 'iori-homer-style', plugins_url( '../', __FILE__ ) . 'assets/css/settingsPage.css', array( 'wp-components' ) );
	}
	
	public function homer_menu_callback() {
		echo '<div id="iori_homer"></div>';
	}
	
	public function homer_add_option_menu() {
		$page_hook_suffix = add_options_page(
			__( 'Homer Features Manager', 'homer' ),
			__( 'Formatting Tools', 'homer' ),
			'manage_options',
			'homer',
			array( $this, 'homer_menu_callback' )
		);
	
		add_action( "admin_print_scripts-{$page_hook_suffix}", array( $this, 'homer_options_assets' ) );
	}
	
	public function homer_register_settings() {
		
		register_setting(
			'homer_settings',
			'homer_hidden_text_spoiler',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => true,
			)
		);
	
		register_setting(
			'homer_settings',
			'homer_text_animation',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => true,
			)
		);
	
		register_setting(
			'homer_settings',
			'homer_uppercase',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => true,
			)
		);
	
		register_setting(
			'homer_settings',
			'homer_lowercase',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => true,
			)
		);
	
		register_setting(
			'homer_settings',
			'homer_underline',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => true,
			)
		);
	
		register_setting(
			'homer_settings',
			'homer_special_characters',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => true,
			)
		);

		function register_animations_settings($animationName) {
			register_setting(
				'homer_settings',
				'homer_' . $animationName . '_available',
				array(
					'type'         => 'boolean',
					'show_in_rest' => true,
					'default'      => true,
				)
			);
			register_setting(
				'homer_settings',
				'homer_' . $animationName . '_favourite_icon',
				array(
					'type'         => 'string',
					'show_in_rest' => true,
					'default'      => 'star-empty',
				)
			);
		}

			register_animations_settings('Wipe');
			register_animations_settings('Typewriter');
			register_animations_settings('Peek In');
			register_animations_settings('Flip');
			register_animations_settings('Bounce');
			register_animations_settings('Swivel');
			register_animations_settings('Rise');
			register_animations_settings('Rotate');
			register_animations_settings('Drop');
			register_animations_settings('Fly In');
			register_animations_settings('Slow Wipe');
			register_animations_settings('Zoom');
			register_animations_settings('Appear');
			register_animations_settings('Grow');
			register_animations_settings('Stretch');
			register_animations_settings('Wave');
			register_animations_settings('Spinner');
			register_animations_settings('LetterFall');

			register_setting(
				'homer_settings',
				'homer_is_animantion_sidebar_settings_panel_enabled',
				array(
					'type'         => 'boolean',
					'show_in_rest' => true,
					'default'      => false,
				)
			);
	}
}