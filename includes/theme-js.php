<?php
/*-----------------------------------------------------------------------------------*/
/* Theme Frontend JavaScript */
/*-----------------------------------------------------------------------------------*/

if ( ! is_admin() ) { add_action( 'wp_print_scripts', 'woothemes_add_javascript' ); }

if ( ! function_exists( 'woothemes_add_javascript' ) ) {
	function woothemes_add_javascript() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'third-party', get_template_directory_uri() . '/includes/js/third-party' . $suffix . '.js', array( 'jquery' ) );
		wp_register_script( 'flexslider', get_template_directory_uri() . '/includes/js/jquery.flexslider' . $suffix . '.js', array( 'jquery' ) );
		wp_register_script( 'prettyPhoto', get_template_directory_uri() . '/includes/js/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ) );
		wp_register_script( 'portfolio', get_template_directory_uri() . '/includes/js/portfolio' . $suffix . '.js', array( 'jquery', 'prettyPhoto' ) );
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/includes/js/modernizr' . $suffix . '.js', array( 'jquery' ), '2.6.2' );

		// Conditionally load the Slider and Portfolio JavaScript, where needed.
		$load_slider_js = false;
		$load_portfolio_js = false;

		if (
			( get_option( 'woo_slider_magazine' ) == 'true' && is_page_template( 'template-magazine.php' ) ) ||
			( get_option( 'woo_slider_biz' ) == 'true' && is_page_template( 'template-biz.php' ) ) ||
			is_page_template( 'template-widgets.php' ) ||
			( is_active_sidebar( 'homepage' ) && ( is_home() || is_front_page() ) )
		) {
			$load_slider_js = true;
		}

		if (
			is_page_template( 'template-portfolio.php' ) ||
			( is_singular() && ( get_post_type() == 'portfolio' ) ) ||
			is_post_type_archive( 'portfolio' ) ||
			is_tax( 'portfolio-gallery' )
		   ) {
			$load_portfolio_js = true;
		}

		if ( is_page_template( 'template-contact.php' ) ) {
			$google_maps_api_key = get_option( 'woo_maps_api_key' );
			wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?key=' . $google_maps_api_key . '', array(), '5.11.2', true );
		}

		// Allow child themes/plugins to load the slider and portfolio JavaScript when they need it.
		$load_slider_js = apply_filters( 'woo_load_slider_js', $load_slider_js );
		$load_portfolio_js = apply_filters( 'woo_load_portfolio_js', $load_portfolio_js );

		if ( $load_slider_js ) { wp_enqueue_script( 'flexslider' ); }
		if ( $load_portfolio_js ) { wp_enqueue_script( 'portfolio' ); }

		do_action( 'woothemes_add_javascript' );

		wp_enqueue_script( 'general', get_template_directory_uri() . '/includes/js/general' . $suffix . '.js', array( 'jquery', 'third-party' ) );

	} // End woothemes_add_javascript()
}

/*-----------------------------------------------------------------------------------*/
/* Theme Frontend CSS */
/*-----------------------------------------------------------------------------------*/

if ( ! is_admin() ) { add_action( 'wp_print_styles', 'woothemes_add_css' ); }

if ( ! function_exists( 'woothemes_add_css' ) ) {
	function woothemes_add_css() {
		global $woo_options;
		wp_register_style( 'prettyPhoto', get_template_directory_uri() . '/includes/css/prettyPhoto.css' );
		wp_register_style( 'non-responsive', get_template_directory_uri() . '/css/non-responsive.css' );

		// Disable prettyPhoto css if WooCommerce is activated and user is on the product page
		$woocommerce_activated 	= is_woocommerce_activated();
		$woocommerce_lightbox	= get_option( 'woocommerce_enable_lightbox' ) == 'yes' ? true : false;
		$woocommerce_product 	= false;
		if ( $woocommerce_activated ) {
			$woocommerce_product = is_product();
		}

		if ( $woocommerce_activated && $woocommerce_product && $woocommerce_lightbox ) {
			wp_deregister_style( 'prettyPhoto' );
		}

		// Conditionally load the Portfolio CSS, where needed.
		$load_portfolio_css = false;

		if (
			is_page_template( 'template-portfolio.php' ) ||
			( is_singular() && ( get_post_type() == 'portfolio' ) ) ||
			is_post_type_archive( 'portfolio' ) ||
			is_tax( 'portfolio-gallery' )
		   ) {
			$load_portfolio_css = true;
		}

		// Allow child themes/plugins to load the portfolio CSS when they need it.
		$load_portfolio_css = apply_filters( 'woo_load_portfolio_css', $load_portfolio_css );

		if ( $load_portfolio_css ) { wp_enqueue_style( 'prettyPhoto' ); }

		do_action( 'woothemes_add_css' );
	} // End woothemes_add_css()
}

/*-----------------------------------------------------------------------------------*/
/* Theme Admin JavaScript */
/*-----------------------------------------------------------------------------------*/

if ( is_admin() ) { add_action( 'admin_print_scripts', 'woothemes_add_admin_javascript' ); }

if ( ! function_exists( 'woothemes_add_admin_javascript' ) ) {
	function woothemes_add_admin_javascript() {
		global $pagenow;
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && ( get_post_type() == 'page' ) ) {
			wp_enqueue_script( 'woo-postmeta-options-custom-toggle', get_template_directory_uri() . '/includes/js/meta-options-custom-toggle' . $suffix . '.js', array( 'jquery' ), '1.0.0' );
		}

	} // End woothemes_add_admin_javascript()
}

/**
 * Enqueue Javascript postMessage handlers for the Customizer.
 *
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since 5.8.0
 */

add_action( 'customize_preview_init', 'woo_customize_preview_js' );

if ( ! function_exists( 'woo_customize_preview_js' ) ) {
	function woo_customize_preview_js() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'woo-customizer', get_template_directory_uri() . '/includes/js/theme-customizer' . $suffix . '.js', array( 'customize-preview' ), '20140801', true );
	} // End woo_customize_preview_js()
}

?>