<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sensei Logic
 */

if ( ! function_exists( 'woo_sensei_support' ) ) {
	function woo_sensei_support() {
		/**
		* Compatibility
		* Declare Sensei support
		*/
		add_theme_support( 'sensei' );
	}
}

if ( ! function_exists( 'woo_sensei_css' ) ) {
	/**
	 * Sensei css
	 * Enqueues Sensei CSS
	 */
	function woo_sensei_css() {
		wp_register_style( 'woo-sensei-css', get_template_directory_uri() . '/includes/integrations/sensei/css/sensei.css' );
		wp_enqueue_style( 'woo-sensei-css' );
	}
}

if ( ! function_exists( 'woo_sensei_remove_pagination' ) ) {
	/**
	 * Sensei pagination
	 * Removes Sensei pagination
	 */
	function woo_sensei_remove_pagination() {
		global $woothemes_sensei;
		remove_action( 'sensei_pagination', array( $woothemes_sensei->frontend, 'sensei_output_content_pagination' ), 10 );
	}
}

if ( ! function_exists( 'woo_sensei_remove_wrappers' ) ) {
	/**
	 * Sensei wrappers
	 * Removes Sensei wrappers
	 */
	function woo_sensei_remove_wrappers() {
		global $woothemes_sensei;
		remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
		remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );
	}
}

if (!function_exists('woo_custom_breadcrumbs_args')) {
	/**
	 * Custom Breadcrumb for Sensei pages
	 * @param  array $args
	 * @return array @args modified
	 */
	function woo_custom_breadcrumbs_args ( $args ) {
		if ( !is_sensei() ) {
			return $args;
		}
		$textdomain = 'woothemes';
		$args = apply_filters( 'canvas_sensei_breadcrumb_args', array( 'show_home' => __( 'Home', $textdomain ) ) );
		return $args;
	} // End woo_custom_breadcrumbs_args()
}

