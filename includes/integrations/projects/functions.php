<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 * @since   1.0.0
 * @return  void
 */
function woo_projects_scripts() {
	wp_register_style( 'woo-projects-css', get_template_directory_uri() . '/includes/integrations/projects/css/projects.css' );
	wp_enqueue_style( 'woo-projects-css' );
}

/**
 * Support Declaration
 * @since   1.0.0
 * @return  void
 */
function woo_projects_support() {
	add_theme_support( 'projects-by-woothemes' );
}

/**
 * Custom Body Class
 * @since   5.9.0
 * @return  array
 */
function woo_projects_old_body_class( $classes ) {
	$settings = woo_get_dynamic_values( array( 'projects_old_look' => 'false' ) );
	if ( 'true' == $settings['projects_old_look'] ) {
		// add 'old-portfolio-look' to the $classes array
		$classes[] = 'old-portfolio-look';
	}
	// return the $classes array
	return $classes;
}

/**
 * Old Portfolio Layout
 * @since   1.0.0
 * @return  void
 */
function woo_projects_maybe_remove_description() {
	$settings = woo_get_dynamic_values( array( 'projects_old_look' => 'false' ) );
	if ( 'false' == $settings['projects_old_look'] ) return;
	remove_action( 'projects_after_loop_item', 'projects_template_short_description', 10 );
}