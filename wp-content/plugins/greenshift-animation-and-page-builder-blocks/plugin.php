<?php
/**
 * Plugin Name: GreenShift - Animation and Page Builder Blocks
 * Description: Animation and page builder for Gutenberg with highest web vitals
 * Author: Wpsoul
 * Author URI: https://greenshiftwp.com
 * Plugin URI: https://greenshiftwp.com
 * Version: 4.8.2
 * Text Domain: greenshift-animation-and-page-builder-blocks
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define Dir URL
define('GREENSHIFT_DIR_URL', plugin_dir_url(__FILE__));
define( 'GREENSHIFT_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * GreenShift Blocks Category
 */
function gspb_greenShift_category( $categories, $post ) {
	return array_merge(
		array(
			array(
				'slug'  => 'GreenShift',
				'title' => __( 'GreenShift'),
			),
		),
		$categories
	);
}

add_filter( 'block_categories_all', 'gspb_greenShift_category', 1, 2 );

// GreenShift Page Templates
class gspb_PageTemplater {
	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new gspb_PageTemplater();
		}

		return self::$instance;

	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the wp 4.7 version attributes metabox
		$post_types = get_post_types();
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				add_filter( "theme_{$post_type}_templates", array( $this, 'gspb_add_new_template' ) );
			}
		}
		

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data',
			array( $this, 'register_project_templates' )
		);

		// Add a filter to the template include to determine if the page has our
		// template assigned and return it's path
		add_filter(
			'template_include',
			array( $this, 'view_project_template' )
		);

		$templatearray = array(
			'page-templates/full-width.php' => 'GreenShift Full Width',
			'page-templates/canvas.php'      => 'GreenShift Clean Canvas',			
		);

		if(defined('GREENSHIFTGSAP_DIR_URL')){
			$templatearray['page-templates/canvas-scroll.php'] = 'Scroll Smooth Clean Canvas';
		}

		// Add your templates to this array.
		$this->templates = $templatearray;

	}

	/**
	 * Adds our template to the page dropdown for v4.7+
	 */
	public function gspb_add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key, 'themes' );

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	}

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {

		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset(
			$this->templates[ get_post_meta(
				$post->ID,
				'_wp_page_template',
				true
			) ]
		) ) {
			return $template;
		}

		$file = GREENSHIFT_DIR_PATH . get_post_meta(
			$post->ID,
			'_wp_page_template',
			true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo wp_kses_post($file);
		}

		// Return template
		return $template;

	}

}

add_action( 'init', array( 'gspb_PageTemplater', 'get_instance' ) );

add_filter(
	'body_class',
	function( $classes ) {
		return array_merge( $classes, array( 'gspbody', 'gspb-bodyfront' ) );
	}
);

add_filter(
	'admin_body_class',
	function( $classes ) {
		$classes .= ' gspbody gspb-bodyadmin ';
		return $classes;
	}
);

require_once GREENSHIFT_DIR_PATH . 'init.php';
require_once GREENSHIFT_DIR_PATH . 'helper.php';
require_once GREENSHIFT_DIR_PATH . 'settings.php';
require_once GREENSHIFT_DIR_PATH . 'patterns.php';

require_once GREENSHIFT_DIR_PATH . '/edd/edd_start.php';
add_action( 'plugins_loaded', 'gspb_GreenShift_plugin_init' );
function gspb_GreenShift_plugin_init() {
	load_plugin_textdomain( 'greenshift-animation-and-page-builder-blocks', false, GREENSHIFT_DIR_PATH. 'lang' ); //translation files
  	if ( class_exists( 'EddLicensePage' ) ) {
    	new EddLicensePage();
  	}
	if ( class_exists( 'GSPB_GreenShift_Settings' ) ) {
		new GSPB_GreenShift_Settings();
	}
}

function gspb_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( '?page=greenshift_dashboard' ) ) );
    }
}
add_action( 'activated_plugin', 'gspb_activation_redirect' );

register_deactivation_hook( __FILE__, 'greenshift_deactivation_hook_function' ); 
function greenshift_deactivation_hook_function() {
    $timestamp = wp_next_scheduled( 'greenshift_check_cron_hook' );
    wp_unschedule_event( $timestamp, 'greenshift_check_cron_hook' );
}

function greenshift_admin_error_notice() {
	//$page = (isset($_GET['page'])) ? $_GET['page'] : '';
	global $current_user;
    $user_id = $current_user->ID;	
    if ( ! get_user_meta($user_id, 'ignore_notices_greenshift149') ) {
		//if ($page=='greenshift' || $page=='greenshift-support' || $page=='greenshift-plugins' || $page=='greenshift-demos' || $page=='vpt_option' ) {
			$class = "error";
			$message = 'This is major update of plugin with removal of Freemius. Please, read how to migrate licenses in <a href="https://www.facebook.com/groups/greenshiftwp/posts/842042560568351" target="_blank">Facebook group news</a>' ;
	    	echo"<div class=\"$class\" style=\"display:block\"> <p>$message <a href=\"?greenshift_nag_ignore=0\">Hide Notice</a></p></div>";
	    //} 
	}
}
add_action( 'admin_notices', 'greenshift_admin_error_notice' );	

add_action('admin_init', 'greenshift_nag_ignore');
function greenshift_nag_ignore() {
	global $current_user;
    $user_id = $current_user->ID;
    if ( isset($_GET['greenshift_nag_ignore']) && '0' == $_GET['greenshift_nag_ignore'] ) {
        add_user_meta($user_id, 'ignore_notices_greenshift149', 'true', true);           
	}
}