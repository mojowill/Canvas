<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------*/
/* Remove Canvas metaboxes from specific custom post types
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'remove_woothemes_meta_boxes_from_wc_bookings' ) ) {
	function remove_woothemes_meta_boxes_from_wc_bookings() {
		remove_meta_box( 'woothemes-settings' , 'wc_booking' , 'normal' );
	}
	add_action( 'admin_menu' , 'remove_woothemes_meta_boxes_from_wc_bookings', 99 );
}

/*-----------------------------------------------------------------------------------*/
/* Declare support for WooCommerce
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woocommerce_support' ) ) {
	function woocommerce_support() {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}

/*-----------------------------------------------------------------------------------*/
/* Styles
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_load_woocommerce_css' ) ) {
	/**
	 * WooCommerce Styles
	 * Enqueue WooCommerce styles
	 */
	function woo_load_woocommerce_css () {
		wp_register_style( 'woocommerce', get_template_directory_uri() . '/includes/integrations/woocommerce/css/woocommerce.css' );
		wp_enqueue_style( 'woocommerce' );
	} // End woo_load_woocommerce_css()
}

if ( ! function_exists( 'woo_wc_disable_css' ) ) {
	function woo_wc_disable_css() {
		/**
		 * Disable WooCommerce styles
		 */
		if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
			// WooCommerce 2.1 or above is active
			add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		} else {
			// WooCommerce is less than 2.1
			define( 'WOOCOMMERCE_USE_CSS', false );
		}
	}
}

/*-----------------------------------------------------------------------------------*/
/* Cart Fragment
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woocommerce_cart_link_fragment' ) ) {
	function woocommerce_cart_link_fragment( $fragments ) {
		global $woocommerce;
		ob_start();
		woo_nav_cart_contents_link();
		$fragments['a.cart-contents'] = ob_get_clean();
		return $fragments;
	} // End woocommerce_cart_link_fragment()
}

/*-----------------------------------------------------------------------------------*/
/* Install
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_install_theme' ) ) {
	function woo_install_theme() {

		update_option( 'woocommerce_thumbnail_image_width', '200' );
		update_option( 'woocommerce_thumbnail_image_height', '200' );
		update_option( 'woocommerce_single_image_width', '500' ); // Single
		update_option( 'woocommerce_single_image_height', '500' ); // Single
		update_option( 'woocommerce_catalog_image_width', '400' ); // Catalog
		update_option( 'woocommerce_catalog_image_height', '400' ); // Catlog

	}
}