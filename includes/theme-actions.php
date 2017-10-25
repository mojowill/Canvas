<?php
	$woo_options = get_option( 'woo_options' );

/*------------------------------------------------------------------------------------

TABLE OF CONTENTS

- Theme Setup
- Woo Conditionals
- Add Google Maps to HEAD
- Load style.css in the <head>
- Add custom styling
- Add layout to body_class output
- WooSlider Setup
- WooSlider Magazine template
- Navigation
- Post More
- Video Embed
- Single Post Author
- Yoast Breadcrumbs
- Subscribe & Connect
- Optional Top Navigation (WP Menus)
- Footer Widgetized Areas
- Add customisable footer areas
- Add customisable post meta
- Add Post Thumbnail to Single posts on Archives
- Post Inside After
- Modify the default "comment" form field.
- Add theme default comment form fields.
- Add theme default comment form arguments.
- Activate shortcode compatibility in our new custom areas.
- woo_content_templates_magazine()
- woo_feedburner_link()
- Help WooTumblog to recognise if it's on the "Magazine" page template
- Enqueue Dynamic CSS
- Load responsive IE scripts
- Load site width CSS in the header
- Function to optionally remove responsive design and load in fallback CSS styling.
- Remove responsive design in IE8
- Adjust the homepage query, if using the "Magazine" page template as the homepage.
- Enable Tumblog
- Full width header
- Full width footer
- Full Width Markup Functions
- Full width body classes
- Optionally load custom logo.
- Optionally load the mobile navigation toggle.
- Add widgetized header area
- Show page content on portfolio page

------------------------------------------------------------------------------------*/

// Check for and enqueue custom styles, if necessary.
add_action( 'woothemes_wp_head_before', 'woo_enqueue_custom_styling', 9 );

// Add layout to body_class output
add_filter( 'body_class','woo_layout_body_class', 10 );

// WooSlider Setup
add_action( 'woo_head','woo_slider', 10 );

// Navigation
add_action( 'woo_header_after','woo_nav', 10 );

// Primary Menu
add_action( 'woo_nav_inside','woo_nav_primary', 10 );

// Subscribe links in navigation
add_action( 'woo_nav_inside','woo_nav_subscribe', 25 );

// Search in navigation
add_action( 'woo_nav_inside','woo_nav_search', 25 );

// Side Navigation wrappers
add_action( 'woo_nav_inside', 'woo_nav_sidenav_start', 15 );
add_action( 'woo_nav_inside', 'woo_nav_sidenav_end', 30 );

// Woo Conditionals
add_action( 'woo_head', 'woo_conditionals', 10 );

// Author Box
add_action( 'wp_head', 'woo_author', 10 );

// Single post navigation
add_action( 'woo_post_after', 'woo_postnav', 10 );

// Add Google Fonts output to HEAD
add_action( 'wp_head', 'woo_google_webfonts', 10 );

// Breadcrumbs
if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) {
	add_action( 'woo_loop_before', 'woo_breadcrumbs', 10 );
}

// Subscribe & Connect
add_action( 'wp_head', 'woo_subscribe_connect_action', 10 );

// Optional Top Navigation (WP Menus)
add_action( 'woo_top', 'woo_top_navigation', 10 );

// Remove responsive design
if ( isset( $woo_options['woo_remove_responsive'] ) && $woo_options['woo_remove_responsive'] == 'true' ) {
	add_action( 'init', 'woo_remove_responsive_design', 10 );
}

// Remove the banner warning about static home page
if ( is_admin() && current_user_can( 'manage_options' ) && ( 0 < intval( get_option( 'page_on_front' ) ) ) ) {
	remove_action( 'wooframework_container_inside', 'wooframework_add_static_front_page_banner' );
}

/*-----------------------------------------------------------------------------------*/
/* Theme Setup */
/*-----------------------------------------------------------------------------------*/
/**
 * Theme Setup
 *
 * This is the general theme setup, where we add_theme_support(), create global variables
 * and setup default generic filters and actions to be used across our theme.
 *
 * @package WooFramework
 * @subpackage Logic
 */

/**
 * Sets up theme defaults and registers support for various WordPress features and plugins.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support for post thumbnails.
 *
 * To override woothemes_setup() in a child theme, add your own woothemes_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for various features / plugins.
 * @uses add_editor_style() To style the visual editor.
 */

add_action( 'after_setup_theme', 'woothemes_setup' );

if ( ! function_exists( 'woothemes_setup' ) ) {
	function woothemes_setup () {

		// Editor Styles
		if ( '' != locate_template( 'editor-style.css' ) ) {
			add_editor_style();
		}

		// This theme uses post thumbnails
		add_theme_support( 'post-thumbnails' );

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Plugin Support
		add_theme_support( 'archives-by-woothemes' );
		add_theme_support( 'features-by-woothemes' );
		add_theme_support( 'our-team-by-woothemes' );
		add_theme_support( 'projects-by-woothemes' );
		add_theme_support( 'sensei' );
		add_theme_support( 'tesitmonials-by-woothemes' );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wooslider' );

		// Custom Background
		add_theme_support( 'custom-background', apply_filters( 'woo_custom_background_args', array(
			'wp-head-callback' 		=> 'woo_custom_background_cb',
			'default-color'         => 'ffffff',
			) )
		);

		$custom_header_width = 960;

		// In case a custom Layouts width is set
		if( null != get_option('woo_layout_width') ) {
			$custom_header_width = get_option('woo_layout_width');
		}

		// Custom Header
		add_theme_support( 'custom-header', apply_filters( 'woo_custom_header_args', array(
			'default-text-color'     => 'fff',
			'width'                  => $custom_header_width,
			'height'                 => 200,
			'flex-height'            => true,
			'wp-head-callback'       => 'woo_custom_header_style',
			'admin-head-callback'    => 'woo_custom_admin_header_style',
			'admin-preview-callback' => 'woo_custom_admin_header_image',
			) )
		);

		// Menu Locations
		if ( function_exists( 'wp_nav_menu') ) {
			add_theme_support( 'nav-menus' );
			register_nav_menus(
				array(
					'primary-menu' 	=> __( 'Primary Menu', 'woothemes' )
					)
				);
			register_nav_menus(
				array(
					'top-menu' 		=> __( 'Top Menu', 'woothemes' )
					)
				);
		}

		// Set the content width based on the theme's design and stylesheet.
		if ( ! isset( $content_width ) ) {
			$content_width = 640;
		}

	} // End woothemes_setup()
}

/**
 * Custom Background Callback.
 *
 * Duplicated from wp-includes/theme.php until there's a better way to change the selector.
 *
 * @see _custom_background_cb()
 * @since 5.8.0
 */
if ( ! function_exists( 'woo_custom_background_cb' ) ) {
	function woo_custom_background_cb() {
		// $background is the saved custom image, or the default image.
		$background 	= set_url_scheme( get_background_image() );

		// $color is the saved custom color.
		// A default has to be specified in style.css. It will not be printed here.
		$color 			= get_background_color();

		if ( $color === get_theme_support( 'custom-background', 'default-color' ) ) {
			$color = false;
		}

		if ( ! $background && ! $color )
			return;

		$style = $color ? "background-color: #$color;" : '';

		if ( $background ) {
			$image 		= " background-image: url('$background');";
			$repeat 	= get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );

			if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) ) {
				$repeat = 'repeat';
			}

			$repeat 	= " background-repeat: $repeat;";
			$position 	= get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );

			if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) ) {
				$position = 'left';
			}

			$position 	= " background-position: top $position;";
			$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );

			if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) ) {
				$attachment = 'scroll';
			}

			$attachment = " background-attachment: $attachment;";

			$style .= $image . $repeat . $position . $attachment;
		}
		?>
		<style type="text/css" id="custom-background-css">
		body.custom-background { <?php echo trim( $style ); ?> }
		</style>
		<?php
	} // woo_custom_background_cb()
}

/**
 * Styles the header image and text displayed on the blog
 *
 * @since 5.8.0
 */
if ( ! function_exists( 'woo_custom_header_style' ) ) {
	function woo_custom_header_style() {
		$text_color = get_header_textcolor();

		// If no custom color for text is set, let's bail.
		if ( display_header_text() && $text_color === get_theme_support( 'custom-header', 'default-text-color' ) )
			return;

		// If we get this far, we have custom styles.
		?>
		<style type="text/css" id="woo-header-css">
		<?php
			// Has the text been hidden?
			if ( ! display_header_text() ) :
		?>
			.site-title,
			.site-description {
				clip: rect(1px 1px 1px 1px); /* IE7 */
				clip: rect(1px, 1px, 1px, 1px);
				position: absolute;
			}
		<?php
			// If the user has set a custom color for the text, use that.
			elseif ( $text_color != get_theme_support( 'custom-header', 'default-text-color' ) ) :
		?>
			#logo .site-title a {
				color: #<?php echo esc_attr( $text_color ); ?>;
			}
		<?php endif; ?>
		</style>
		<?php
	} // woo_custom_header_style()
}

/**
 * Style the header image displayed on the Appearance > Header screen.
 *
 * @since 5.8.0
 */
if ( ! function_exists( 'woo_custom_admin_header_style' ) ) {
	function woo_custom_admin_header_style() {
	?>
		<style type="text/css" id="woo-admin-header-css">
		.appearance_page_custom-header #headimg {
			border: none;
			max-width: 980px;
			min-height: 48px;
			padding: 40px 0;
		}
		#headimg h1 {
			font: bold 28px/1.2em "Helvetica Neue", Helvetica, sans-serif;
			color: #000;
			display: block;
			line-height: inherit;
			margin-bottom: 5px;
			font-weight: bold;
		}
		#headimg h1 a {
			font: bold 40px/1em "Helvetica Neue", Helvetica, sans-serif;
			color: #222222;
			text-decoration: none;
		}
		#headimg img {
			vertical-align: middle;
		}
		</style>
	<?php
	} // woo_custom_admin_header_style()
}

/**
 * Create the custom header image markup displayed on the Appearance > Header screen.
 *
 * @since 5.8.0
 */
if ( ! function_exists( 'woo_custom_admin_header_image' ) ) {
	function woo_custom_admin_header_image() {
	?>
		<div id="headimg" <?php if ( get_header_image() ) : ?>style="background-image:url(<?php echo header_image(); ?>);"<?php endif; ?>>
			<h1 class="displaying-header-text"><a id="name"<?php echo sprintf( ' style="color:#%s;"', get_header_textcolor() ); ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		</div>
	<?php
	} // woo_custom_admin_header_image()
}

/**
 * Output custom header image as a background.
 *
 * @since 5.8.0
 */

add_action( 'woothemes_wp_head_before', 'woo_custom_header_bg_output', 10 );

if ( ! function_exists( 'woo_custom_header_bg_output' ) ) {
	function woo_custom_header_bg_output() {
		if ( get_header_image() ) {
		?>
		<style type="text/css" id="woo-header-bg-css">
		#header { background-image:url(<?php echo header_image(); ?>); }
		</style>
		<?php
		}
	} // woo_custom_header_bg_output()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Conditionals */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_conditionals' ) ) {
	function woo_conditionals () {
		// Video Embed
		if( is_single() && ( 'portfolio' != get_post_type() ) ) {
			add_action( 'woo_post_inside_before', 'canvas_get_embed' );
		}

		// Post More
		if ( ! is_singular() && ! is_404() || is_page_template( 'template-blog.php' ) || is_page_template( 'template-magazine.php' ) || is_page_template( 'template-widgets.php' ) ) {
			add_action( 'woo_post_inside_after', 'woo_post_more' );
		}

		// Tumblog Content
		if ( 'true' == get_option( 'woo_woo_tumblog_switch' ) ) {
			add_action( 'woo_tumblog_content_before', 'woo_tumblog_content' );
			add_action( 'woo_tumblog_content_after', 'woo_tumblog_content' );
		}
	} // End woo_conditionals()
}

/*-----------------------------------------------------------------------------------*/
/* Load style.css in the <head> */
/*-----------------------------------------------------------------------------------*/

if ( ! is_admin() ) { add_action( 'wp_enqueue_scripts', 'woo_load_frontend_css', 20 ); }

if ( ! function_exists( 'woo_load_frontend_css' ) ) {
function woo_load_frontend_css () {

	// Assign the Canvas version to a var
	$theme 				= wp_get_theme();
	$canvas_version 	= $theme['Version'];

	wp_register_style( 'theme-stylesheet', get_stylesheet_uri(), array(), $canvas_version, 'all' );
	wp_enqueue_style( 'theme-stylesheet' );
} // End woo_load_frontend_css()
}

/*-----------------------------------------------------------------------------------*/
/* Load responsive <meta> tags in the <head> */
/*-----------------------------------------------------------------------------------*/

add_action( 'wp_head', 'woo_load_responsive_meta_tags', 1 );

if ( ! function_exists( 'woo_load_responsive_meta_tags' ) ) {
function woo_load_responsive_meta_tags () {
	$html = '';

	/* Remove this if not responsive design */
	$html .= "\n" . '<!--  Mobile viewport scale -->' . "\n";
	$html .= '<meta name="viewport" content="width=device-width, initial-scale=1"/>' . "\n";

	echo $html;
} // End woo_load_responsive_meta_tags()
}

/*-----------------------------------------------------------------------------------*/
/* // Add custom styling */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_custom_styling' ) ) {
function woo_custom_styling() {
	global $woo_options;

	$output = '';

	// Logo
	if ( isset( $woo_options['woo_logo'] ) && $woo_options['woo_logo'] ) $output .= '#logo .site-title, #logo .site-description { display:none; }' . "\n";

	// Check if we want to generate the custom styling or not.
	if ( ! isset( $woo_options['woo_style_disable'] ) ) {
		$woo_options['woo_style_disable'] = 'false';
	}

	if ( 'true' == $woo_options['woo_style_disable'] ) {
		// We still output the CSS related to the custom logo, if one exists.
		if ( '' != $output ) { echo $output; }
		return;
	}

	// Init options
	$options_init = array( 'woo_style_bg', 'woo_style_bg_image', 'woo_style_bg_image_repeat', 'woo_style_bg_image_pos', 'woo_style_bg_image_attach', 'woo_border_top', 'woo_style_border',
						   'woo_link_color', 'woo_link_hover_color', 'woo_button_color', 'woo_button_hover_color', 'woo_header_bg', 'woo_header_bg_image', 'woo_header_bg_image_repeat',
						   'woo_header_border', 'woo_header_margin_top', 'woo_header_margin_bottom', 'woo_header_padding_top', 'woo_header_padding_bottom', 'woo_header_padding_left',
						   'woo_header_padding_right', 'woo_font_logo', 'woo_font_desc', 'woo_layout_boxed', 'woo_style_box_bg', 'woo_box_margin_top', 'woo_box_margin_bottom',
						   'woo_box_border_tb', 'woo_box_border_lr', 'woo_box_border_radius', 'woo_box_shadow', 'woo_full_header_full_width_bg', 'woo_full_header_bg_image',
						   'woo_full_header_bg_image_repeat', 'woo_nav_bg', 'woo_nav_divider_border', 'woo_nav_border_top', 'woo_nav_border_bot', 'woo_foot_full_width_widget_bg',
						   'woo_footer_full_width_bg', 'woo_footer_border_top', 'woo_font_text', 'woo_font_h1', 'woo_font_h2', 'woo_font_h3', 'woo_font_h4', 'woo_font_h5', 'woo_font_h6',
						   'woo_font_post_title', 'woo_font_post_meta', 'woo_font_post_text', 'woo_font_post_more', 'woo_post_more_border_top', 'woo_post_more_border_bottom',
						   'woo_post_comments_bg', 'woo_post_author_border_top', 'woo_post_author_border_bottom', 'woo_post_author_border_lr', 'woo_post_author_border_radius',
						   'woo_post_author_bg', 'woo_pagenav_font', 'woo_pagenav_bg', 'woo_pagenav_border_top', 'woo_pagenav_border_bottom', 'woo_widget_font_title',
						   'woo_widget_font_text', 'woo_widget_padding_tb', 'woo_widget_padding_lr', 'woo_widget_bg', 'woo_widget_border', 'woo_widget_title_border', 'woo_widget_border_radius',
						   'woo_widget_tabs_bg', 'woo_widget_tabs_bg_inside', 'woo_widget_tabs_font', 'woo_widget_tabs_font_meta', 'woo_nav_bg', 'woo_nav_font', 'woo_nav_hover', 'woo_nav_hover_bg',
						   'woo_nav_divider_border', 'woo_nav_dropdown_border', 'woo_nav_border_lr', 'woo_nav_border_radius', 'woo_nav_border_top', 'woo_nav_border_bot', 'woo_nav_margin_top',
						   'woo_nav_margin_bottom', 'woo_top_nav_bg', 'woo_top_nav_hover', 'woo_top_nav_hover_bg', 'woo_top_nav_font', 'woo_footer_font', 'woo_footer_bg', 'woo_footer_border_top',
						   'woo_footer_border_bottom', 'woo_footer_border_lr', 'woo_footer_border_radius', 'woo_slider_magazine_font_title', 'woo_slider_magazine_font_excerpt', 'woo_magazine_grid_font_post_title',
						   'woo_slider_biz_font_title', 'woo_slider_biz_font_excerpt', 'woo_slider_biz_overlay', 'woo_archive_header_font', 'woo_archive_header_border_bottom'
						);

	foreach ( $options_init as $option ) {
		if ( isset( $woo_options[ $option ] ) ) {
			${ $option } = $woo_options[ $option ];
		} else {
			${ $option } = false;
		}
	}

		// Layout styling
		$body = '';
		if ($woo_style_bg)
			$body .= 'background-color:'.$woo_style_bg.';';
		if ($woo_style_bg_image)
			$body .= 'background-image:url('.$woo_style_bg_image.');';
		if ($woo_style_bg_image_repeat)
			$body .= 'background-repeat:'.$woo_style_bg_image_repeat.';';
		if ($woo_style_bg_image_pos)
			$body .= 'background-position:'.$woo_style_bg_image_pos.';';
		if ($woo_style_bg_image_attach)
			$body .= 'background-attachment:'.$woo_style_bg_image_attach.';';
		if ($woo_border_top && $woo_border_top['width'] >= 0)
			$body .= 'border-top:'.$woo_border_top["width"].'px '.$woo_border_top["style"].' '.$woo_border_top["color"].';';

		if ( $body != '' )
			$output .= 'body {'. $body . '}'. "\n";

		if ( $woo_style_border )
			$output .= 'hr, .entry img, img.thumbnail, .entry .wp-caption, #footer-widgets, #comments, #comments .comment.thread-even, #comments ul.children li, .entry h1{border-color:'. $woo_style_border . '}'. "\n";


		// General styling
		if ($woo_link_color)
			$output .= 'a:link, a:visited, #loopedSlider a.flex-prev:hover, #loopedSlider a.flex-next:hover {color:'.$woo_link_color.'} .quantity .plus, .quantity .minus {background-color: ' . $woo_link_color . ';}' . "\n";
		if ($woo_link_hover_color)
			$output .= 'a:hover, .post-more a:hover, .post-meta a:hover, .post p.tags a:hover {color:'.$woo_link_hover_color.'}' . "\n";
		if ($woo_button_color)
			$output .= 'body #wrapper .button, body #wrapper #content .button, body #wrapper #content .button:visited, body #wrapper #content .reply a, body #wrapper #content #respond .form-submit input#submit, input[type=submit], body #wrapper #searchsubmit, #navigation ul.cart .button, body #wrapper .woo-sc-button {border: none; background:'.$woo_button_color.'}' . "\n";
		if ($woo_button_hover_color)
			$output .= 'body #wrapper .button:hover, body #wrapper #content .button:hover, body #wrapper #content .reply a:hover, body #wrapper #content #respond .form-submit input#submit:hover, input[type=submit]:hover, body #wrapper #searchsubmit:hover, #navigation ul.cart .button:hover, body #wrapper .woo-sc-button:hover {border: none; background:'.$woo_button_hover_color.'}' . "\n";
		// Header styling
		$header_css = '';
		if ( $woo_header_bg )
			$header_css .= 'background-color:'.$woo_header_bg.';';
		if ( $woo_header_bg_image )
			$header_css .= 'background-image:url('.$woo_header_bg_image.');';
		if ( $woo_header_bg_image_repeat )
			$header_css .= 'background-repeat:'.$woo_header_bg_image_repeat.';background-position:left top;';
		if ( $woo_header_margin_top <> '' || $woo_header_margin_bottom <> '' )
			$header_css .= 'margin-top:'.$woo_header_margin_top.'px;margin-bottom:'.$woo_header_margin_bottom.'px;';
		if ( $woo_header_padding_top <> '' || $woo_header_padding_bottom <> '' )
			$header_css .= 'padding-top:'.$woo_header_padding_top.'px;padding-bottom:'.$woo_header_padding_bottom.'px;';
		if ( $woo_header_border && $woo_header_border['width'] >= 0)
			$header_css .= 'border:'.$woo_header_border["width"].'px '.$woo_header_border["style"].' '.$woo_header_border["color"].';';

		if ( $header_css != '' )
			$output .= '#header {'. $header_css . '}'. "\n";

		if ( $woo_header_padding_left <> '' )
			$output .= '#logo {padding-left:'.$woo_header_padding_left.'px;}';
		if ( $woo_header_padding_right <> '' )
			$output .= '.header-widget {padding-right:'.$woo_header_padding_right.'px;}'. "\n";
		if ( $woo_font_logo )
			$output .= '#logo .site-title a {' . woo_generate_font_css( $woo_font_logo ) . '}' . "\n";
		if ( $woo_font_desc )
			$output .= '#logo .site-description {' . woo_generate_font_css( $woo_font_desc ) . '}' . "\n";


		// Boxed styling
		$wrapper = '';
		if ($woo_layout_boxed == "true") {
			//$wrapper .= 'margin:0 auto;padding:0 0 20px 0;width:'.get_option('woo_layout_width').';';
			if ( get_option('woo_layout_width') == '940px' )
				$wrapper .= 'padding-left:20px; padding-right:20px;';
			else
				$wrapper .= 'padding-left:30px; padding-right:30px;';
		}
		if ($woo_layout_boxed == "true" && $woo_style_box_bg)
			$wrapper .= 'background-color:'.$woo_style_box_bg.';';
		if ($woo_layout_boxed == "true" && ($woo_box_margin_top || $woo_box_margin_bottom) )
			$wrapper .= 'margin-top:'.$woo_box_margin_top.'px;margin-bottom:'.$woo_box_margin_bottom.'px;';
		if ($woo_layout_boxed == "true" && $woo_box_border_tb["width"] > 0 )
			$wrapper .= 'border-top:'.$woo_box_border_tb["width"].'px '.$woo_box_border_tb["style"].' '.$woo_box_border_tb["color"].';border-bottom:'.$woo_box_border_tb["width"].'px '.$woo_box_border_tb["style"].' '.$woo_box_border_tb["color"].';';
		if ($woo_layout_boxed == "true" && $woo_box_border_lr["width"] > 0 )
			$wrapper .= 'border-left:'.$woo_box_border_lr["width"].'px '.$woo_box_border_lr["style"].' '.$woo_box_border_lr["color"].';border-right:'.$woo_box_border_lr["width"].'px '.$woo_box_border_lr["style"].' '.$woo_box_border_lr["color"].';';
		if ( $woo_layout_boxed == "true" && $woo_box_border_radius )
			$wrapper .= 'border-radius:'.$woo_box_border_radius.';';
		if ( $woo_layout_boxed == "true" && $woo_box_shadow == "true" )
			$wrapper .= 'box-shadow: 0px 1px 5px rgba(0,0,0,.1);';

		if ( $wrapper != '' )
			$output .= '#inner-wrapper {'. $wrapper . '} .col-full { width: auto; } @media only screen and (max-width:767px) { #inner-wrapper { margin:0; border-radius:none; padding-left:1em; padding-right: 1em; border: none; } } '. "\n";


		// Full width layout
		if ( $woo_layout_boxed != "true" && (isset( $woo_options['woo_header_full_width'] ) && ( $woo_options['woo_header_full_width']  == 'true'  )  ||  isset( $woo_options['woo_footer_full_width'] ) && ( $woo_options['woo_footer_full_width'] == 'true' ) ) ) {

			if ( isset( $woo_options['woo_header_full_width'] ) && $woo_options['woo_header_full_width'] == 'true' ) {

				if ( $woo_full_header_full_width_bg )
					$output .= '#header-container{background-color:' . $woo_full_header_full_width_bg . ';}';

				if ( $woo_full_header_bg_image )
					$output .= '#header-container{background-image:url('.$woo_full_header_bg_image.');background-repeat:'.$woo_full_header_bg_image_repeat.';background-position:top center;}';

				if ( $woo_nav_bg )
					$output .= '#nav-container{background:' . $woo_nav_bg . ';}';

				if ( $woo_nav_border_top && $woo_nav_border_top["width"] >= 0 )
					$output .= '#nav-container{border-top:'.$woo_nav_border_top["width"].'px '.$woo_nav_border_top["style"].' '.$woo_nav_border_top["color"].';border-bottom:'.$woo_nav_border_bot["width"].'px '.$woo_nav_border_bot["style"].' '.$woo_nav_border_bot["color"].';border-left:none;border-right:none;}';

				if ( $woo_nav_divider_border && $woo_nav_divider_border["width"] >= 0 )
					$output .= '#nav-container #navigation ul#main-nav > li:first-child{border-left: '.$woo_nav_divider_border["width"].'px '.$woo_nav_divider_border["style"].' '.$woo_nav_divider_border["color"].';}';

			}

			if ( isset( $woo_options['woo_footer_full_width'] ) && ( 'true' == $woo_options['woo_footer_full_width'] ) ) {

				if ( $woo_foot_full_width_widget_bg )
					$output .= '#footer-widgets-container{background-color:' . $woo_foot_full_width_widget_bg . '}#footer-widgets{border:none;}';

				if ( $woo_footer_full_width_bg )
					$output .= '#footer-container{background-color:' . $woo_footer_full_width_bg . '}';

				if ( $woo_footer_border_top && $woo_footer_border_top["width"] >= 0 )
					$output .= '#footer-container{border-top:'.$woo_footer_border_top["width"].'px '.$woo_footer_border_top["style"].' '.$woo_footer_border_top["color"].';}#footer {border-width: 0 !important;}';

			}
			$output .= "\n";

		}

		// General Typography
		if ( $woo_font_text )
			$output .= 'body, p { ' . woo_generate_font_css( $woo_font_text, 1.5 ) . ' }' . "\n";
		if ( $woo_font_h1 )
			$output .= 'h1 { ' . woo_generate_font_css( $woo_font_h1, 1.2 ) . ' }';
		if ( $woo_font_h2 )
			$output .= 'h2 { ' . woo_generate_font_css( $woo_font_h2, 1.2 ) . ' }';
		if ( $woo_font_h3 )
			$output .= 'h3 { ' . woo_generate_font_css( $woo_font_h3, 1.2 ) . ' }';
		if ( $woo_font_h4 )
			$output .= 'h4 { ' . woo_generate_font_css( $woo_font_h4, 1.2 ) . ' }';
		if ( $woo_font_h5 )
			$output .= 'h5 { ' . woo_generate_font_css( $woo_font_h5, 1.2 ) . ' }';
		if ( $woo_font_h6 )
			$output .= 'h6 { ' . woo_generate_font_css( $woo_font_h6, 1.2 ) . ' }' . "\n";

		// Post Styling
		if ( $woo_font_post_title )
			$output .= '.page-title, .post .title, .page .title {'.woo_generate_font_css( $woo_font_post_title, 1.1 ).'}' . "\n";
			$output .= '.post .title a:link, .post .title a:visited, .page .title a:link, .page .title a:visited {color:'.$woo_font_post_title["color"].'}' . "\n";
		if ( $woo_font_post_meta )
			$output .= '.post-meta { ' . woo_generate_font_css( $woo_font_post_meta, 1.5 ) . ' }' . "\n";
		if ( $woo_font_post_text )
			$output .= '.entry, .entry p{ ' . woo_generate_font_css( $woo_font_post_text, 1.5 ) . ' }' . "\n";
		$post_more_border = '';
		if ( $woo_font_post_more )
			$post_more_border .= 'font:'.$woo_font_post_more["style"].' '.$woo_font_post_more["size"].$woo_font_post_more["unit"].'/1.5em '.stripslashes($woo_font_post_more["face"]).';color:'.$woo_font_post_more["color"].';';
		if ( $woo_post_more_border_top )
			$post_more_border .= 'border-top:'.$woo_post_more_border_top["width"].'px '.$woo_post_more_border_top["style"].' '.$woo_post_more_border_top["color"].';';
		if ( $woo_post_more_border_bottom )
			$post_more_border .= 'border-bottom:'.$woo_post_more_border_bottom["width"].'px '.$woo_post_more_border_bottom["style"].' '.$woo_post_more_border_bottom["color"].';';
		if ( $post_more_border )
		$output .= '.post-more {'.$post_more_border .'}' . "\n";

		$post_author = '';
		if ( $woo_post_author_border_top )
			$post_author .= 'border-top:'.$woo_post_author_border_top["width"].'px '.$woo_post_author_border_top["style"].' '.$woo_post_author_border_top["color"].';';
		if ( $woo_post_author_border_bottom )
			$post_author .= 'border-bottom:'.$woo_post_author_border_bottom["width"].'px '.$woo_post_author_border_bottom["style"].' '.$woo_post_author_border_bottom["color"].';';
		if ( $woo_post_author_border_lr )
			$post_author .= 'border-left:'.$woo_post_author_border_lr["width"].'px '.$woo_post_author_border_lr["style"].' '.$woo_post_author_border_lr["color"].';border-right:'.$woo_post_author_border_lr["width"].'px '.$woo_post_author_border_lr["style"].' '.$woo_post_author_border_lr["color"].';';
		if ( $woo_post_author_border_radius )
			$post_author .= 'border-radius:'.$woo_post_author_border_radius.';-moz-border-radius:'.$woo_post_author_border_radius.';-webkit-border-radius:'.$woo_post_author_border_radius.';';
		if ( $woo_post_author_bg )
			$post_author .= 'background-color:'.$woo_post_author_bg;

		if ( $post_author )
			$output .= '#post-author, #connect {'.$post_author .'}' . "\n";

		if ( $woo_post_comments_bg )
			$output .= '#comments .comment.thread-even {background-color:'.$woo_post_comments_bg.';}' . "\n";

		// Page Nav Styling
		$pagenav_css = '';
		if ( $woo_pagenav_bg )
			$pagenav_css .= 'background-color:'.$woo_pagenav_bg.';';
		if ( $woo_pagenav_border_top && $woo_pagenav_border_top["width"] > 0 )
			$pagenav_css .= 'border-top:'.$woo_pagenav_border_top["width"].'px '.$woo_pagenav_border_top["style"].' '.$woo_pagenav_border_top["color"].';';
		if ( $woo_pagenav_border_bottom && $woo_pagenav_border_bottom["width"] > 0 )
			$pagenav_css .= 'border-bottom:'.$woo_pagenav_border_bottom["width"].'px '.$woo_pagenav_border_bottom["style"].' '.$woo_pagenav_border_bottom["color"].';';
		if ( $pagenav_css != '' )
			$output .= '.nav-entries, .woo-pagination {'. $pagenav_css . ' padding: 12px 0px; }'. "\n";
		if ( $woo_pagenav_font ) {
			$output .= '.nav-entries a, .woo-pagination { ' . woo_generate_font_css( $woo_pagenav_font ) . ' }' . "\n";
			$output .= '.woo-pagination a, .woo-pagination a:hover {color:'.$woo_pagenav_font["color"].'!important}' . "\n";
		}

		// Widget Styling
		$h3_css = '';
		if ( $woo_widget_font_title )
			$h3_css .= 'font:'.$woo_widget_font_title["style"].' '.$woo_widget_font_title["size"].$woo_widget_font_title["unit"].'/1.2em '.stripslashes($woo_widget_font_title["face"]).';color:'.$woo_widget_font_title["color"].';';
		if ( $woo_widget_title_border )
			$h3_css .= 'border-bottom:'.$woo_widget_title_border["width"].'px '.$woo_widget_title_border["style"].' '.$woo_widget_title_border["color"].';';
		if ( isset( $woo_widget_title_border["width"] ) AND $woo_widget_title_border["width"] == 0 )
			$h3_css .= 'margin-bottom:0;';

		if ( $h3_css != '' )
			$output .= '.widget h3 {'. $h3_css . '}'. "\n";

		if ( $woo_widget_title_border )
			$output .= '.widget_recent_comments li, #twitter li { border-color: '.$woo_widget_title_border["color"].';}'. "\n";

		if ( $woo_widget_font_text )
			$output .= '.widget p, .widget .textwidget { ' . woo_generate_font_css( $woo_widget_font_text, 1.5 ) . ' }' . "\n";

		$widget_css = '';
		if ( $woo_widget_font_text )
			$widget_css .= 'font:'.$woo_widget_font_text["style"].' '.$woo_widget_font_text["size"].$woo_widget_font_text["unit"].'/1.5em '.stripslashes($woo_widget_font_text["face"]).';color:'.$woo_widget_font_text["color"].';';
		if ( $woo_widget_padding_tb || $woo_widget_padding_lr )
			$widget_css .= 'padding:'.$woo_widget_padding_tb.'px '.$woo_widget_padding_lr.'px;';
		if ( $woo_widget_bg )
			$widget_css .= 'background-color:'.$woo_widget_bg.';';
		if ( $woo_widget_border["width"] > 0 )
			$widget_css .= 'border:'.$woo_widget_border["width"].'px '.$woo_widget_border["style"].' '.$woo_widget_border["color"].';';
		if ( $woo_widget_border_radius )
			$widget_css .= 'border-radius:'.$woo_widget_border_radius.';-moz-border-radius:'.$woo_widget_border_radius.';-webkit-border-radius:'.$woo_widget_border_radius.';';

		if ( $widget_css != '' )
			$output .= '.widget {'. $widget_css . '}'. "\n";

		if ( $woo_widget_border["width"] > 0 )
			$output .= '#tabs {border:'.$woo_widget_border["width"].'px '.$woo_widget_border["style"].' '.$woo_widget_border["color"].';}'. "\n";

		// Tabs Widget
		if ( $woo_widget_tabs_bg )
			$output .= '#tabs, .widget_woodojo_tabs .tabbable {background-color:'.$woo_widget_tabs_bg.';}'. "\n";
		if ( $woo_widget_tabs_bg_inside )
			$output .= '#tabs .inside, #tabs ul.wooTabs li a.selected, #tabs ul.wooTabs li a:hover {background-color:'.$woo_widget_tabs_bg_inside.';}'. "\n";
		if ( $woo_widget_tabs_font )
			$output .= '#tabs .inside li a, .widget_woodojo_tabs .tabbable .tab-pane li a { ' . woo_generate_font_css( $woo_widget_tabs_font, 1.5 ) . ' }'. "\n";
		if ( $woo_widget_tabs_font_meta )
			$output .= '#tabs .inside li span.meta, .widget_woodojo_tabs .tabbable .tab-pane li span.meta { ' . woo_generate_font_css( $woo_widget_tabs_font_meta, 1.5 ) . ' }'. "\n";
			$output .= '#tabs ul.wooTabs li a, .widget_woodojo_tabs .tabbable .nav-tabs li a { ' . woo_generate_font_css( $woo_widget_tabs_font_meta, 2 ) . ' }'. "\n";

		//Navigation
		global $is_IE;
		if ( !$is_IE )
			$output .= '@media only screen and (min-width:768px) {' . "\n";
		if ( $woo_nav_font )
			$output .= 'ul.nav li a, #navigation ul.rss a, #navigation ul.cart a.cart-contents, #navigation .cart-contents #navigation ul.rss, #navigation ul.nav-search, #navigation ul.nav-search a { ' . woo_generate_font_css( $woo_nav_font, 1.2 ) . ' } #navigation ul.rss li a:before, #navigation ul.nav-search a.search-contents:before { color:' . $woo_nav_font['color'] . ';}' . "\n";
		if ( $woo_nav_hover )
			$output .= '#navigation ul.nav > li a:hover, #navigation ul.nav > li:hover a, #navigation ul.nav li ul li a, #navigation ul.cart > li:hover > a, #navigation ul.cart > li > ul > div, #navigation ul.cart > li > ul > div p, #navigation ul.cart > li > ul span, #navigation ul.cart .cart_list a, #navigation ul.nav li.current_page_item a, #navigation ul.nav li.current_page_parent a, #navigation ul.nav li.current-menu-ancestor a, #navigation ul.nav li.current-cat a, #navigation ul.nav li.current-menu-item a { color:'.$woo_nav_hover.'!important; }' . "\n";
		if ( $woo_nav_hover_bg )
			$output .= '#navigation ul.nav > li a:hover, #navigation ul.nav > li:hover, #navigation ul.nav li ul, #navigation ul.cart li:hover a.cart-contents, #navigation ul.nav-search li:hover a.search-contents, #navigation ul.nav-search a.search-contents + ul, #navigation ul.cart a.cart-contents + ul, #navigation ul.nav li.current_page_item a, #navigation ul.nav li.current_page_parent a, #navigation ul.nav li.current-menu-ancestor a, #navigation ul.nav li.current-cat a, #navigation ul.nav li.current-menu-item a{background-color:'.$woo_nav_hover_bg.'!important}' . "\n";

		if ( $woo_nav_dropdown_border && $woo_nav_dropdown_border["width"] >= 0 ) {
			$output .= '#navigation ul.nav li ul, #navigation ul.cart > li > ul > div  { border: '.$woo_nav_dropdown_border["width"].'px '.$woo_nav_dropdown_border["style"].' '.$woo_nav_dropdown_border["color"].'; }' . "\n";
			if ($woo_nav_dropdown_border["width"] == 0) {
				$output .= '#navigation ul.nav > li:hover > ul  { left: 0; }' . "\n";
			}
		}

		if ( $woo_nav_divider_border && $woo_nav_divider_border["width"] >= 0 ) {
			$output .= '#navigation ul.nav > li  { border-right: '.$woo_nav_divider_border["width"].'px '.$woo_nav_divider_border["style"].' '.$woo_nav_divider_border["color"].'; }';
			if ($woo_nav_divider_border["width"] == 0) {
				$output .= '#navigation ul.nav > li:hover > ul  { left: 0; }' . "\n";
			}
		}

		$navigation_css = '';
		if ( $woo_nav_bg )
			$navigation_css .= 'background:'.$woo_nav_bg.';';
		if ( $woo_nav_border_top && $woo_nav_border_top["width"] >= 0 )
			$navigation_css .= 'border-top:'.$woo_nav_border_top["width"].'px '.$woo_nav_border_top["style"].' '.$woo_nav_border_top["color"].';border-bottom:'.$woo_nav_border_bot["width"].'px '.$woo_nav_border_bot["style"].' '.$woo_nav_border_bot["color"].';border-left:'.$woo_nav_border_lr["width"].'px '.$woo_nav_border_lr["style"].' '.$woo_nav_border_lr["color"].';border-right:'.$woo_nav_border_lr["width"].'px '.$woo_nav_border_lr["style"].' '.$woo_nav_border_lr["color"].';';
		if ( $woo_nav_border_bot && $woo_nav_border_bot["width"] == 0 )
			$output .= '#navigation { box-shadow: none; -moz-box-shadow: none; -webkit-box-shadow: none; }';
		if ( $woo_nav_border_radius )
			$navigation_css .= 'border-radius:'.$woo_nav_border_radius.'; -moz-border-radius:'.$woo_nav_border_radius.'; -webkit-border-radius:'.$woo_nav_border_radius.';';

		if ( $woo_nav_border_radius )
			$output .= '#navigation ul li:first-child, #navigation ul li:first-child a { border-radius:'.$woo_nav_border_radius.' 0 0 '.$woo_nav_border_radius.'; -moz-border-radius:'.$woo_nav_border_radius.' 0 0 '.$woo_nav_border_radius.'; -webkit-border-radius:'.$woo_nav_border_radius.' 0 0 '.$woo_nav_border_radius.'; }' . "\n";

		if ( '' != $woo_nav_margin_top  || '' != $woo_nav_margin_bottom ) {
			if ( isset( $woo_options[ 'woo_header_full_width' ] ) && 'true' == $woo_options[ 'woo_header_full_width' ]  ) {
				$navigation_css .= 'margin-top:0;margin-bottom:0;';
				$output .= '#nav-container { margin-top:'.$woo_nav_margin_top.'px;margin-bottom:'.$woo_nav_margin_bottom.'px; }';
			} else {
				$navigation_css .= 'margin-top:'.$woo_nav_margin_top.'px;margin-bottom:'.$woo_nav_margin_bottom.'px;';
			}
		}

		if ( $navigation_css != '' )
			$output .= '#navigation {'. $navigation_css . '}'. "\n";

		if ( $woo_top_nav_bg )
			$output .= '#top, #top ul.nav li ul li a:hover { background:'.$woo_top_nav_bg.';}'. "\n";

		if ( $woo_top_nav_hover )
			$output .= '#top ul.nav li a:hover, #top ul.nav li.current_page_item a, #top ul.nav li.current_page_parent a,#top ul.nav li.current-menu-ancestor a,#top ul.nav li.current-cat a,#top ul.nav li.current-menu-item a,#top ul.nav li.sfHover, #top ul.nav li ul, #top ul.nav > li:hover a, #top ul.nav li ul li a { color:'.$woo_top_nav_hover.'!important;}'. "\n";

		if ( $woo_top_nav_hover_bg )
			$output .= '#top ul.nav li a:hover, #top ul.nav li.current_page_item a, #top ul.nav li.current_page_parent a,#top ul.nav li.current-menu-ancestor a,#top ul.nav li.current-cat a,#top ul.nav li.current-menu-item a,#top ul.nav li.sfHover, #top ul.nav li ul, #top ul.nav > li:hover { background:'.$woo_top_nav_hover_bg.';}'. "\n";

		if ( $woo_top_nav_font ) {
			$output .= '#top ul.nav li a { ' . woo_generate_font_css( $woo_top_nav_font, 1.6 ) . ' }' . "\n";
			if ( isset( $woo_top_nav_font['color'] ) && strlen( $woo_top_nav_font['color'] ) == 7 ) {
				$output .= '#top ul.nav li.parent > a:after { border-top-color:'. esc_attr( $woo_top_nav_font['color'] ) .';}'. "\n";
			}
		}
		if ( !$is_IE )
			$output .= '}' . "\n";

		// Footer
		if ( $woo_footer_font )
			$output .= '#footer, #footer p { ' . woo_generate_font_css( $woo_footer_font, 1.4 ) . ' }' . "\n";
		$footer_css = '';
		if ( $woo_footer_bg )
			$footer_css .= 'background-color:'.$woo_footer_bg.';';
		if ( $woo_footer_border_top )
			$footer_css .= 'border-top:'.$woo_footer_border_top["width"].'px '.$woo_footer_border_top["style"].' '.$woo_footer_border_top["color"].';';
		if ( $woo_footer_border_bottom )
			$footer_css .= 'border-bottom:'.$woo_footer_border_bottom["width"].'px '.$woo_footer_border_bottom["style"].' '.$woo_footer_border_bottom["color"].';';
		if ( $woo_footer_border_lr )
			$footer_css .= 'border-left:'.$woo_footer_border_lr["width"].'px '.$woo_footer_border_lr["style"].' '.$woo_footer_border_lr["color"].';border-right:'.$woo_footer_border_lr["width"].'px '.$woo_footer_border_lr["style"].' '.$woo_footer_border_lr["color"].';';
		if ( $woo_footer_border_radius )
			$footer_css .= 'border-radius:'.$woo_footer_border_radius.'; -moz-border-radius:'.$woo_footer_border_radius.'; -webkit-border-radius:'.$woo_footer_border_radius.';';

		if ( $footer_css != '' )
			$output .= '#footer {'. $footer_css . '}' . "\n";

		// Magazine Template
		if ( $woo_slider_magazine_font_title ) {
			$output .= '.magazine #loopedSlider .content h2.title a { ' . woo_generate_font_css( $woo_slider_magazine_font_title ) . ' }'. "\n";
			// WooSlider Integration
			$output .= '.wooslider-theme-magazine .slide-title a { ' . woo_generate_font_css( $woo_slider_magazine_font_title ) . ' }'. "\n";
		}
		if ( $woo_slider_magazine_font_excerpt ) {
			$output .= '.magazine #loopedSlider .content .excerpt p { ' . woo_generate_font_css( $woo_slider_magazine_font_excerpt, 1.5 ) . ' }'. "\n";
			// WooSlider Integration
			$output .= '.wooslider-theme-magazine .slide-content p, .wooslider-theme-magazine .slide-excerpt p { ' . woo_generate_font_css( $woo_slider_magazine_font_excerpt, 1.5 ) . ' }'. "\n";

		}
		if ( $woo_magazine_grid_font_post_title ) {
			$output .= '.magazine .block .post .title a {' . woo_generate_font_css( $woo_magazine_grid_font_post_title, 1.2 ) . ' }'. "\n";
		}

		// Business Template
		if ( $woo_slider_biz_font_title ) {
			$output .= '#loopedSlider.business-slider .content h2 { ' . woo_generate_font_css( $woo_slider_biz_font_title ) . ' }'. "\n";
			$output .= '#loopedSlider.business-slider .content h2.title a { ' . woo_generate_font_css( $woo_slider_biz_font_title ) . ' }'. "\n";
			// WooSlider Integration
			$output .= '.wooslider-theme-business .has-featured-image .slide-title { ' . woo_generate_font_css( $woo_slider_biz_font_title ) . ' }'. "\n";
			$output .= '.wooslider-theme-business .has-featured-image .slide-title a { ' . woo_generate_font_css( $woo_slider_biz_font_title ) . ' }'. "\n";
		}
		if ( $woo_slider_biz_font_excerpt ) {
			$output .= '#wrapper #loopedSlider.business-slider .content p { ' . woo_generate_font_css( $woo_slider_biz_font_excerpt, 1.5 ) . ' }'. "\n";
			// WooSlider Integration
			$output .= '.wooslider-theme-business .has-featured-image .slide-content p { ' . woo_generate_font_css( $woo_slider_biz_font_excerpt, 1.5 ) . ' }'. "\n";
			$output .= '.wooslider-theme-business .has-featured-image .slide-excerpt p { ' . woo_generate_font_css( $woo_slider_biz_font_excerpt, 1.5 ) . ' }'. "\n";

		}

		// Slider overlay
		if ( $woo_slider_biz_overlay == 'left' || $woo_slider_biz_overlay == 'right' || $woo_slider_biz_overlay == 'center' || $woo_slider_biz_overlay == 'full' || $woo_slider_biz_overlay == 'none' ) {
			$output .= '@media only screen and (min-width:768px) {' . "\n";
			if ( $woo_slider_biz_overlay && $woo_slider_biz_overlay == 'left' )
				$output .= '#wrapper #loopedSlider.business-slider .content { width: 40%; top: 2.5em; bottom: inherit; left:0; right: inherit; text-align: left; }'. "\n";
			if ( $woo_slider_biz_overlay && $woo_slider_biz_overlay == 'right' )
				$output .= '#wrapper #loopedSlider.business-slider .content { width: 40%; top: 2.5em; bottom: inherit; right:0; left: inherit; text-align: right; }'. "\n";
			if ( $woo_slider_biz_overlay && $woo_slider_biz_overlay == 'center' )
				$output .= '#wrapper #loopedSlider.business-slider .content { width: 50%; top: 20%; bottom: inherit; }'. "\n";
			if ( $woo_slider_biz_overlay && $woo_slider_biz_overlay == 'full' )
				$output .= '#wrapper #loopedSlider.business-slider .content { top: 0; padding-top: 7%; }'. "\n";
			if ( $woo_slider_biz_overlay && $woo_slider_biz_overlay == 'none' )
				$output .= '#wrapper #loopedSlider.business-slider .content { background: none; width: 50%; top: 20%; bottom: inherit; }'. "\n";
			$output .= '}' . "\n";
		}

		// Archive Header
		if ( $woo_archive_header_font )
			$output .= '.archive_header { ' . woo_generate_font_css( $woo_archive_header_font ) . ' }'. "\n";
		if ( $woo_archive_header_border_bottom )
			$output .= '.archive_header {border-bottom:'.$woo_archive_header_border_bottom["width"].'px '.$woo_archive_header_border_bottom["style"].' '.$woo_archive_header_border_bottom["color"].';}'. "\n";
		if ( isset( $woo_options['woo_archive_header_disable_rss'] ) && $woo_options['woo_archive_header_disable_rss'] == "true" )
			$output .= '.archive_header .catrss { display:none; }' . "\n";

	// Output styles
	if (isset($output)) {
		// $output = "\n<!-- Woo Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n<!-- /Woo Custom Styling -->\n\n";
		echo $output;
	}
} // End woo_custom_styling()
}

// Returns proper font css output
if ( ! function_exists( 'woo_generate_font_css' ) ) {
	function woo_generate_font_css( $option, $em = '1' ) {

		// Test if font-face is a Google font
		global $google_fonts;
		foreach ( $google_fonts as $google_font ) {

			// Add single quotation marks to font name and default arial sans-serif ending
			if ( $option['face'] == $google_font['name'] )
				$option['face'] = "'" . $option['face'] . "', arial, sans-serif";

		} // END foreach

		if ( !@$option['style'] && !@$option['size'] && !@$option['unit'] && !@$option['color'] )
			return 'font-family: '.stripslashes(str_replace( '&quot;', '', $option['face'] )).';';
		else
			return 'font:'.$option['style'].' '.$option['size'].$option['unit'].'/'.$em.'em '.stripslashes(str_replace( '&quot;', '', $option['face'] )).';color:'.$option['color'].';';
	} // End woo_generate_font_css()
}

/*-----------------------------------------------------------------------------------*/
/* Determine what layout to use */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_get_layout' ) ) {
	function woo_get_layout() {

		global $post, $wp_query, $woo_options;

		// Reset the query
		if ( is_main_query() ) {
			wp_reset_query();
		}

		// Set default global layout
		$layout = 'two-col-left';
		if ( '' != get_option( 'woo_layout' ) ) {
			$layout = get_option( 'woo_layout' );
		}

		// Single post layout
		if ( is_singular() ) {
			// Get layout setting from single post Custom Settings panel
			if ( '' != get_post_meta( $post->ID, 'layout', true ) ) {
				$layout = get_post_meta( $post->ID, 'layout', true );

			// Portfolio single post layout option.
			} elseif ( 'portfolio' == get_post_type() ) {
				if ( '' != get_option( 'woo_portfolio_layout_single' ) ) {
					$layout = get_option( 'woo_portfolio_layout_single' );
				}

			} elseif ( 'project' == get_post_type() ) {
				if ( '' != get_option( 'woo_projects_layout_single' ) ) {
					$layout = get_option( 'woo_projects_layout_single' );
				} else {
					$layout = get_option( 'woo_layout' );
				}
			}
		}

		// Portfolio gallery layout option.
		if ( is_tax( 'portfolio-gallery' ) || is_post_type_archive( 'portfolio' ) || is_page_template( 'template-portfolio.php' ) ) {
			if ( '' != get_option( 'woo_portfolio_layout' ) ) {
				$layout = get_option( 'woo_portfolio_layout' );
			}
		}

		// Projects gallery layout option.
		if ( is_tax( 'project-category' ) || is_post_type_archive( 'project' ) ) {
			if ( '' != get_option( 'woo_projects_layout' ) ) {
				$layout = get_option( 'woo_projects_layout' );
			} else {
				$layout = get_option( 'woo_layout' );
			}
		}

		// WooCommerce Layout
		if ( is_woocommerce_activated() && is_woocommerce() ) {
			// Set defaul layout
			if ( '' != get_option( 'woo_wc_layout' ) ) {
				$layout = get_option( 'woo_wc_layout' );
			}
			// WooCommerce single post/page
			if ( is_singular() ) {
				// Get layout setting from single post Custom Settings panel
				if ( '' != get_post_meta( $post->ID, 'layout', true ) ) {
					$layout = get_post_meta( $post->ID, 'layout', true );
				}
			}
		}

		// Blog Page - Get layout setting from single post Custom Settings panel
		if ( is_home() ) {
		  	if ( '' != get_post_meta( $post->ID, 'layout', true ) ) {
				$layout = get_post_meta( $post->ID, 'layout', true );
			}
		}

		return $layout;

	} // End woo_get_layout()
}

/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_layout_body_class' ) ) {
	function woo_layout_body_class( $classes ) {
		global $post, $wp_query;

		if ( is_tax( 'portfolio-gallery' ) || is_post_type_archive( 'portfolio' ) || is_page_template( 'template-portfolio.php' ) || ( is_singular() && get_post_type() == 'portfolio' ) ) {
			$classes[] = 'portfolio-component';
		}

		// Specify site width
		$width = intval( str_replace( 'px', '', get_option( 'woo_layout_width', '960' ) ) );

		// Add classes to body_class() output
		$classes[] = woo_get_layout();
		$classes[] = 'width-' . $width;
		$classes[] = woo_get_layout() . '-' . $width;
		return $classes;
	} // End woo_layout_body_class()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Slider Setup */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_slider' ) ) {
	function woo_slider( $load_slider_js = false ) {
		global $woo_options;

		$load_slider_js = false;

		if ( ( is_page_template( 'template-biz.php' ) && isset( $woo_options['woo_slider_biz'] ) && $woo_options['woo_slider_biz'] == 'true' ) ||
			 ( is_page_template( 'template-magazine.php' ) && isset( $woo_options['woo_slider_magazine'] ) && $woo_options['woo_slider_magazine'] == 'true' ) ||
			   is_page_template( 'template-widgets.php' ) ||
			   is_active_sidebar( 'homepage' ) ) { $load_slider_js = true; }

		// Allow child themes/plugins to load the slider JavaScript when they need it.
		$load_slider_js = (bool)apply_filters( 'woo_load_slider_js', $load_slider_js );


		if ( $load_slider_js != false ) {

		// Default slider settings.
		$defaults = array(
							'autoStart' => 0,
							'hoverPause' => 'false',
							'containerClick' => 'false',
							'slideSpeed' => 600,
							'canAutoStart' => 'false',
							'next' => 'next',
							'prev' => 'previous',
							'container' => 'slides',
							'generatePagination' => 'false',
							'crossfade' => 'true',
							'fadeSpeed' => 600,
							'effect' => 'slide'
						 );

		// Dynamic settings from the "Theme Options" screen.
		$args = array();

		if ( isset( $woo_options['woo_slider_pagination'] ) && $woo_options['woo_slider_pagination'] == 'true' ) { $args['generatePagination'] = 'true'; }
		if ( isset( $woo_options['woo_slider_effect'] ) && $woo_options['woo_slider_effect'] != '' ) { $args['effect'] = $woo_options['woo_slider_effect']; }
		if ( isset( $woo_options['woo_slider_hover'] ) && $woo_options['woo_slider_hover'] == 'true' ) { $args['hoverPause'] = 'true'; }
		if ( isset( $woo_options['woo_slider_containerclick'] ) && $woo_options['woo_slider_containerclick'] == 'true' ) { $args['containerClick'] = 'true'; }
		if ( isset( $woo_options['woo_slider_speed'] ) && $woo_options['woo_slider_speed'] != '' ) { $args['slideSpeed'] = $woo_options['woo_slider_speed'] * 1000; }
		if ( isset( $woo_options['woo_slider_speed'] ) && $woo_options['woo_slider_speed'] != '' ) { $args['fadeSpeed'] = $woo_options['woo_slider_speed'] * 1000; }
		if ( isset( $woo_options['woo_slider_auto'] ) && $woo_options['woo_slider_auto'] == 'true' ) {
			$args['canAutoStart'] = 'true';
			$args['autoStart'] = $woo_options['woo_slider_interval'] * 1000;
		}

		// Merge the arguments with defaults.
		$args = wp_parse_args( $args, $defaults );

		// Allow child themes/plugins to filter these arguments.
		$args = apply_filters( 'woo_slider_args', $args );

	?>
	<!-- Woo Slider Setup -->
	<script type="text/javascript">
	jQuery(window).load(function() {
		var args = {};
		args.useCSS = false;
		<?php if ( $args['effect'] == 'fade' ) { ?>args.animation = 'fade';
		<?php } else { ?>args.animation = 'slide';<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( $args['canAutoStart'] == 'true' ) { ?>args.slideshow = true;
		<?php } else { ?>args.slideshow = false;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( intval( $args['autoStart'] ) > 0 ) { ?>args.slideshowSpeed = <?php echo intval( $args['autoStart'] ) ?>;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( intval( $args['slideSpeed'] ) >= 0 ) { ?>args.animationSpeed = <?php echo intval( $args['slideSpeed'] ) ?>;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( $args['generatePagination'] == 'true' ) { ?>args.controlNav = true;
		<?php } else { ?>args.controlNav = false;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( $args['hoverPause'] == 'true' ) { ?>args.pauseOnHover = true;
		<?php } else { ?>args.pauseOnHover = false;<?php } ?>
		<?php echo "\n"; ?>
		<?php if ( apply_filters( 'woo_slider_autoheight', true ) ) { ?>args.smoothHeight = true;<?php } ?>
		<?php echo "\n"; ?>
		args.manualControls = '.pagination-wrap .flex-control-nav > li';
		<?php echo "\n"; ?>
		args.start = function ( slider ) {
			slider.next( '.slider-pagination' ).fadeIn();
		}
		args.prevText = '<span class="fa fa-angle-left"></span>';
		args.nextText = '<span class="fa fa-angle-right"></span>';

		jQuery( '.woo-slideshow' ).each( function ( i ) {
			jQuery( this ).flexslider( args );
		});
	});
	</script>
	<!-- /Woo Slider Setup -->
	<?php
		}
	} // End woo_slider()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Slider Magazine */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_slider_magazine' ) ) {
	function woo_slider_magazine( $args = null, $tags = null ) {
		global $woo_options, $wp_query;

		// Exit if this isn't the first page in the loop
		if ( is_paged() ) return;

		// If WooSlider is enabled, let's use it instead
		if ( class_exists( 'WooSlider' ) ) {
			if ( version_compare( get_option( 'wooslider-version' ), '2.0.2' ) >= 0 ) {
				echo '<div class="wooslider-slider-magazine">';
				woo_wooslider_magazine();
				echo '</div><!-- /.wooslider-slider-magazine -->';
				return;
			}
		}

		// This is where our output will be added.
		$html = '';

		// Default slider settings.
		$defaults = array(
							'id' => 'loopedSlider',
							'echo' => true,
							'excerpt_length' => '15',
							'pagination' => false,
							'width' => '960',
							'order' => 'ASC',
							'posts_per_page' => '5'
						 );

		// Setup width of slider and images
		$width = '623';

		if ( 'one-col' == woo_get_layout() && isset( $woo_options['woo_layout_width'] ) && '' != $woo_options['woo_layout_width'] ) {
			$width = intval( str_replace( 'px', '', $woo_options['woo_layout_width'] ) );
		}

		// Setup slider tags array
		$slider_tags = array();
		if ( is_array( $tags ) && ( 0 < count( $tags ) ) ) {
			$slider_tags = $tags;
		}

		if ( ! is_array( $tags ) && '' != $tags && ! is_null( $tags ) ) {
			$slider_tags = explode( ',', $tags );
		}

		if ( 0 >= count( $slider_tags ) ) {
			$slider_tags = explode( ',', $woo_options['woo_slider_magazine_tags'] ); // Tags to be shown
		}

		if ( 0 < count( $slider_tags ) ) {
			foreach ( $slider_tags as $tags ) {
				$tag = get_term_by( 'name', trim($tags), 'post_tag', 'ARRAY_A' );
				if ( $tag['term_id'] > 0 )
					$tag_array[] = $tag['term_id'];
			}
		}

		if ( empty( $tag_array ) ) {
			echo do_shortcode( '[box type="alert"]' . __( 'Setup slider by adding <strong>Post Tags</strong> in <em>Magazine Template > Posts Slider</em>.', 'woothemes' ) . '[/box]' );
			return;
		}

		// Setup the slider CSS class.
		$slider_css = '';

		if ( isset( $woo_options['woo_slider_pagination'] ) && $woo_options['woo_slider_pagination'] == 'true' ) {
			$slider_css = ' class="magazine-slider has-pagination woo-slideshow"';
		} else {
			$slider_css = ' class="magazine-slider woo-slideshow"';
		}

		// Setup the number of posts to show.
		$posts_per_page = $woo_options['woo_slider_magazine_entries'];
		if ( $posts_per_page != '' ) { $defaults['posts_per_page'] = $posts_per_page; }

		// Setup the excerpt length.
		$excerpt_length = $woo_options['woo_slider_magazine_excerpt_length'];
		if ( $excerpt_length != '' ) { $defaults['excerpt_length'] = $excerpt_length; }

		if ( $width > 0 && ( isset( $args['width'] ) || empty( $args['width'] ) ) ) { $defaults['width'] = $width; }

		// Merge the arguments with defaults.
		$args = wp_parse_args( $args, $defaults );

		if ( ( ( isset($args['width']) ) && ( ( $args['width'] <= 0 ) || ( $args['width'] == '')  ) ) || ( !isset($args['width']) ) ) {	$args['width'] = '100'; }

		// Allow child themes/plugins to filter these arguments.
		$args = apply_filters( 'woo_magazine_slider_args', $args );

		// Begin setting up HTML output.
		$image_args = 'width=' . $args['width'] . '&link=img&return=true&noheight=true';

		if ( apply_filters( 'woo_slider_autoheight', true ) ) {
			$html .= '<div id="' . $args['id'] . '"' . $slider_css . ' style="height:auto;">' . "\n";

	    } else {
			$html .= '<div id="' . $args['id'] . '"' . $slider_css . ' style="max-height:' . apply_filters( 'woo_slider_height', 350 ) . 'px;">' . "\n";
			$image_args .= '&height=' . apply_filters( 'woo_slider_height', 350 );
		}

	$saved = $wp_query; $query = new WP_Query( array( 'tag__in' => $tag_array, 'posts_per_page' => $args['posts_per_page'] ) );

	if ( $query->have_posts() ) : $count = 0;

			if ( apply_filters( 'woo_slider_autoheight', true ) ) {
				$html .= '<ul class="slides">' . "\n";
			} else {
				$html .= '<ul class="slides" style="max-height:' . apply_filters( 'woo_slider_height', 350 ) . 'px;">' . "\n";
			}

	        while ( $query->have_posts() ) : $query->the_post(); global $post; $shownposts[$count] = $post->ID; $count++;

	           $styles = 'width: ' . $args['width'] . 'px;';
				if ( $count >= 2 ) { $styles .= ' display:none;'; } else { $styles = ''; }

				$url = get_permalink( $post->ID );

	            $html .= '<li id="slide-' . esc_attr( $post->ID ) . '" class="slide slide-number-' . esc_attr( $count ) . '" style="' . $styles . '">' . "\n";
					$html .= '<a href="' . $url . '" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">' . woo_image( $image_args ) . '</a>' . "\n";
	                $html .= '<div class="content">' . "\n";
	                if ( $woo_options['woo_slider_magazine_title'] == 'true' ) {
	                	$html .= '<h2 class="title"><a href="' . $url . '" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">' . get_the_title( $post->ID ) . '</a></h2>'; }

	                if ( $woo_options['woo_slider_magazine_excerpt'] == 'true' ) {
	                	$excerpt = woo_text_trim( get_the_excerpt(), $excerpt_length );
	                	if ( '' != $excerpt )
	                		$html .= '<div class="excerpt"><p>' . $excerpt . '</p></div>' . "\n";
	                }

	                $html .= '</div>' . "\n";

	            $html .= '</li>' . "\n";

	       endwhile;
		endif; $wp_query = $saved;

	    $html .= '</ul><!-- /.slides -->' . "\n";
	    $html .= '</div>' . "\n";

	if ( isset( $woo_options['woo_slider_pagination'] ) && $woo_options['woo_slider_pagination'] == 'true' ) {
		$html .= '<div class="pagination-wrap slider-pagination"><ol class="flex-control-nav flex-control-paging">';
		for ( $i = 0; $i < $count; $i++ ) {
			$html .= '<li><a>' . ( $i + 1 ) . '</a></li>';
		}
		$html .= '</ol></div>';
	}

    	if ( get_option( 'woo_exclude' ) != $shownposts ) { update_option( "woo_exclude", $shownposts ); }

		if ( $args['echo'] ) {
			echo $html;
		}

		return $html;
	} // End woo_slider_magazine()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Get Slides */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_slider_get_slides' ) ) {
	function woo_slider_get_slides( $args ) {
		$defaults = array( 'posts_per_page' => '5', 'order' => 'DESC', 'slide_page_terms' => '',  'use_slide_page' => false );
		$args = wp_parse_args( (array)$args, $defaults );
		$query_args = array( 'post_type' => 'slide', 'suppress_filters' => false );
		if ( in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ) ) ) {
			$query_args['order'] = strtoupper( $args['order'] );
		}
		if ( 0 < intval( $args['posts_per_page'] ) ) {
			$query_args['posts_per_page'] = intval( $args['posts_per_page'] );
		}
		if ( false != $args['use_slide_page'] ) {
			$slide_type = 'slug';
			if ( is_numeric( $args['slide_page_terms'] ) ) $slide_type = 'id';
			$query_args['tax_query'] = array(
											array( 'taxonomy' => 'slide-page', 'field' => 'id', 'terms' => intval( $args['slide_page_terms']) )
											);
		}

		$slides = false;

		$query = get_posts( $query_args );

		if ( ! is_wp_error( $query ) && ( 0 < count( $query ) ) ) {
			$slides = $query;
		}

		return $slides;
	} // End woo_slider_get_slides()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Slider Business */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_slider_biz' ) ) {
	function woo_slider_biz( $args = null ) {

		global $woo_options, $post;

		// Exit if this isn't the first page in the loop
		if ( is_paged() ) return;

		// If WooSlider is enabled, let's use it instead
		if ( class_exists( 'WooSlider' ) ) {
			if ( version_compare( get_option( 'wooslider-version' ), '2.0.2' ) >= 0 ) {
				echo '<div class="wooslider-slider-business">';
				woo_wooslider_business();
				echo '</div><!-- /.wooslider-slider-business -->';
				return;
			}
		}

		$options = woo_get_dynamic_values( array( 'slider_biz_slide_group' => '0' ) );

		// Default slider settings.
		$defaults = array(
							'id' => 'loopedSlider',
							'pagination' => false,
							'width' => '960',
							'order' => 'ASC',
							'posts_per_page' => '5',
							'slide_page' => $options['slider_biz_slide_group'],
							'use_slide_page' => false
						 );

		if ( '0' != $defaults['slide_page'] ) $defaults['use_slide_page'] = true;

		// Setup the "Slide Group", if one is set.
		if ( isset( $post->ID ) ) {
			$slide_page = '0';
			$stored_slide_page = get_post_meta( $post->ID, '_slide-page', true );

			if ( $stored_slide_page != '' && '0' != $stored_slide_page ) {
				$slide_page = $stored_slide_page;
				$defaults['use_slide_page'] = true; // Instruct the slider to apply the necessary conditional.
				$defaults['slide_page'] = $slide_page;
			}
		}

		// Setup width of slider and images.
		if ( isset( $woo_options['woo_slider_biz_full'] ) && 'true' == $woo_options['woo_slider_biz_full'] ) {
			$width = '1600';
		} else {
			$layout = woo_get_layout();
			$layout_width = get_option('woo_layout_width');

			$width = intval( $layout_width );
		}

		// Setup the number of posts to show.
		$posts_per_page = '';
		if ( isset( $woo_options['woo_slider_biz_number'] ) && $woo_options['woo_slider_biz_number'] != '' ) {
			$posts_per_page = $woo_options['woo_slider_biz_number'];
			$defaults['posts_per_page'] = $posts_per_page;
		}

		// Setup the order of posts.
		$post_order = '';
		if ( isset( $woo_options['woo_slider_biz_order'] ) && $woo_options['woo_slider_biz_order'] != '' ) {
			$post_order = $woo_options['woo_slider_biz_order'];
			$defaults['order'] = $post_order;
		}

		if ( ( 0 < $width ) && !isset( $args['width'] ) ) { $defaults['width'] = $width; }

		// Merge the arguments with defaults.
		$args = wp_parse_args( $args, $defaults );

		if ( ( ( isset( $args['width'] ) ) && ( ( $args['width'] <= 0 ) || ( $args['width'] == '' )  ) ) || ( ! isset( $args['width'] ) ) ) {	$args['width'] = '100'; }

		// Allow child themes/plugins to filter these arguments.
		$args = apply_filters( 'woo_biz_slider_args', $args );

		// Disable auto image functionality
		$auto_img = false;
		if ( get_option( 'woo_auto_img' ) == 'true' ) {
			update_option( 'woo_auto_img', 'false' );
			$auto_img = true;
		}

		// Disable placeholder image functionality
		$placeholder_img = get_option( 'framework_woo_default_image' );
		if ( $placeholder_img ) {
			update_option( 'framework_woo_default_image', '' );
		}

		// Setup the slider CSS class.
		$slider_css = '';
		if ( isset( $woo_options['woo_slider_pagination'] ) && $woo_options['woo_slider_pagination'] == 'true' ) {
			$slider_css = 'business-slider has-pagination woo-slideshow';
		} else {
			$slider_css = 'business-slider woo-slideshow';
		}

		// Setup the slider height.
		if ( apply_filters( 'woo_slider_autoheight', true ) ) {
			$slider_height = 'height:auto';
	    } else {
			$slider_height = apply_filters( 'woo_slider_height', 350 );
		}

		// Slide Styles
		$slide_styles = 'width: ' . $args['width'] . 'px;';

		$query_args = array(
						'posts_per_page' => $posts_per_page,
						'order' => $post_order,
						'use_slide_page' => $args['use_slide_page'],
						'slide_page_terms' => $args['slide_page']
					);

		// Retrieve the slides, based on the query arguments.
		$slides = woo_slider_get_slides( $query_args );

		if ( false == $slides ) {
			echo do_shortcode( '[box type="alert"]' . __( 'Please add some slider posts via Slides > Add New', 'woothemes' ) . '[/box]');
			return;
		}

		if ( ( count( $slides ) < 1 ) ) {
			echo do_shortcode( '[box type="alert"]' . __( 'Please note that this slider requires 2 or more slides in order to function. Please add another slide.', 'woothemes' ) . '[/box]');
			return;
		}

		$view_args = array(
					'id' => $args['id'],
					'width' => $args['width'],
					'height' => $slider_height,
					'container_css' => $slider_css,
					'slide_styles' => $slide_styles
				);

		// Allow child themes/plugins to filter these arguments.
		$view_args = apply_filters( 'woo_slider_biz_view_args', $view_args );

		// Display slider
		woo_slider_biz_view( $view_args, $slides );

		// Enable auto img functionality
		if ( $auto_img )
			update_option( 'woo_auto_img', 'true' );

		// Enable placeholder functionality
		if ( '' != $placeholder_img )
			update_option( 'framework_woo_default_image', $placeholder_img );

	} // End woo_slider_biz()
}

/*-----------------------------------------------------------------------------------*/
/* Woo Business Slider View */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_slider_biz_view' ) ) {
	function woo_slider_biz_view( $args = null, $slides = null ) {

		global $woo_options, $post;

		// Default slider settings.
		$defaults = array(
							'id' => 'loopedSlider',
							'width' => '960',
							'container_css' => '',
							'slide_styles' => ''
						);

		// Merge the arguments with defaults.
		$args = wp_parse_args( $args, $defaults );

		// Init slide count
		$count = 0;

	?>

	<?php do_action('woo_biz_slider_before'); ?>

	<div id="<?php echo esc_attr( $args['id'] ); ?>"<?php if ( '' != $args['container_css'] ): ?> class="<?php echo esc_attr( $args['container_css'] ); ?>"<?php endif; ?><?php if ( !apply_filters( 'woo_slider_autoheight', true ) ): ?> style="height: <?php echo apply_filters( 'woo_slider_height', 350 ); ?>px;"<?php endif; ?>>

		<ul class="slides"<?php if ( !apply_filters( 'woo_slider_autoheight', true ) ): ?> style="height: <?php echo apply_filters( 'woo_slider_height', 350 ); ?>px;"<?php endif; ?>>
			<?php $original_slide_styles = $args['slide_styles']; ?>
			<?php foreach ( $slides as $k => $post ) { setup_postdata( $post ); $count++; ?>

			<?php
				// Slide Styles
				if ( $count >= 2 ) { $args['slide_styles'] = $original_slide_styles . ' display:none;'; } else { $args['slide_styles'] = ''; }
			?>

			<li id="slide-<?php echo esc_attr( $post->ID ); ?>" class="slide slide-number-<?php echo esc_attr( $count ); ?>" <?php if ( '' != $args['slide_styles'] ): ?>style="<?php echo esc_attr( $args['slide_styles'] ); ?>"<?php endif; ?>>

				<?php
					$type = woo_image('return=true');
					if ( $type ):
						$url = get_post_meta( $post->ID, 'url', true );
				?>

					<?php if ( '' != $url ): ?><a href="<?php echo esc_url( $url ); ?>" title="<?php the_title_attribute(); ?>"><?php endif; ?>
					<?php woo_image( 'width=' . $args['width'] . '&link=img&noheight=true' ); ?>
					<?php if ( '' != $url ): ?></a><?php endif; ?>

					<?php if ( ( isset( $woo_options['woo_slider_biz_title'] ) && 'true' == $woo_options['woo_slider_biz_title'] ) || '' != get_the_content() ): ?>
					<div class="content">

						<?php if ( isset( $woo_options['woo_slider_biz_title'] ) && 'true' == $woo_options['woo_slider_biz_title'] ): ?>
						<div class="title">
							<h2 class="title">
								<?php if ( '' != $url ): ?><a href="<?php echo esc_url( $url ); ?>" title="<?php the_title_attribute(); ?>"><?php endif; ?>
								<?php the_title(); ?>
								<?php if ( '' != $url ): ?></a><?php endif; ?>
							</h2>
						</div>
						<?php endif; ?>

						<?php
							$content = '';
							if ( '' != $post->post_excerpt ) {
								$content = $post->post_excerpt;
							} else {
								$content = $post->post_content;
							}
							$content = do_shortcode( $content );
							$content = wpautop( $content );
						?>

						<?php if ( '' != $content ): ?>
						<div class="excerpt">
							<?php echo $content; ?>
						</div><!-- /.excerpt -->
						<?php endif; ?>

					</div><!-- /.content -->
					<?php endif; ?>

				<?php else: ?>

					<section class="entry col-full">
						<?php the_content(); ?>
					</section>

				<?php endif; ?>

			</li><!-- /.slide-number-<?php echo esc_attr( $count ); ?> -->

			<?php } // End foreach ?>

			<?php wp_reset_postdata();  ?>

		</ul><!-- /.slides -->

	</div><!-- /#<?php echo $args['id']; ?> -->

	<?php if ( isset( $woo_options['woo_slider_pagination'] ) && $woo_options['woo_slider_pagination'] == 'true' ) : ?>
		<div class="pagination-wrap slider-pagination">
			<ol class="flex-control-nav flex-control-paging">
				<?php for ( $i = 0; $i < $count; $i++ ): ?>
					<li><a><?php echo ( $i + 1 ) ?></a></li>
				<?php endfor; ?>
			</ol>
		</div>
	<?php endif; ?>

	<?php do_action('woo_biz_slider_after'); ?>

<?php
	} // End woo_slider_biz_view()
}

/*-----------------------------------------------------------------------------------*/
/* Navigation */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_nav' ) ) {
function woo_nav() {
	global $woo_options;
	woo_nav_before();
?>
<nav id="navigation" class="col-full" role="navigation">

	<?php
		$menu_class = 'menus';
		$number_icons = 0;

		$icons = array(
			'woo_nav_rss',
			'woo_nav_search',
			'woo_header_cart_link'
		);

		foreach ( $icons as $icon ) {
			if ( isset( $woo_options[ $icon ] ) && 'true' == $woo_options[ $icon ] ) {
				$number_icons++;
			}
		}

		if ( isset( $woo_options[ 'woo_subscribe_email' ] ) && '' != $woo_options[ 'woo_subscribe_email' ] ) {
			$number_icons++;
		}

		if ( 0 < $number_icons ) {
			$menu_class .= ' nav-icons nav-icons-' . $number_icons;

			if ( isset( $woo_options[ 'woo_header_cart_link' ] ) && 'true' == $woo_options['woo_header_cart_link'] ) {
				if ( isset( $woo_options[ 'woo_header_cart_total' ] ) && 'true' == $woo_options[ 'woo_header_cart_total' ] ) {
					$menu_class .= ' cart-extended';
				}
			}
		}
	?>

	<section class="<?php echo $menu_class; ?>">

	<?php woo_nav_inside(); ?>

	</section><!-- /.menus -->

	<a href="#top" class="nav-close"><span><?php _e( 'Return to Content', 'woothemes' ); ?></span></a>

</nav>
<?php
	woo_nav_after();
} // End woo_nav()
}

/*-----------------------------------------------------------------------------------*/
/* Primary menu */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_nav_primary' ) ) {
function woo_nav_primary() {
?>
	<a href="<?php echo home_url(); ?>" class="nav-home"><span><?php _e( 'Home', 'woothemes' ); ?></span></a>

	<?php
	if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) {
		echo '<h3>' . woo_get_menu_name( 'primary-menu' ) . '</h3>';
		wp_nav_menu( array( 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
	} else {
	?>
		<ul id="main-nav" class="nav fl">
			<?php
			if ( get_option( 'woo_custom_nav_menu' ) == 'true' ) {
				if ( function_exists( 'woo_custom_navigation_output' ) ) { woo_custom_navigation_output( 'name=Woo Menu 1' ); }
			} else { ?>

				<?php if ( is_page() ) { $highlight = 'page_item'; } else { $highlight = 'page_item current_page_item'; } ?>
				<li class="<?php echo esc_attr( $highlight ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Home', 'woothemes' ); ?></a></li>
				<?php wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); ?>
			<?php } ?>
		</ul><!-- /#nav -->
	<?php }

} // End woo_nav_primary()
}


/*-----------------------------------------------------------------------------------*/
/* Add Side Navigation wrappers */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_nav_sidenav_start' ) ) {
function woo_nav_sidenav_start() {
?>
	<div class="side-nav">
<?php
} // End woo_nav_sidenav_start()
}

if ( ! function_exists( 'woo_nav_sidenav_end' ) ) {
function woo_nav_sidenav_end() {
?>
	</div><!-- /#side-nav -->
<?php
} // End woo_nav_sidenav_start()
}

/*-----------------------------------------------------------------------------------*/
/* Add subscription links to the navigation bar */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_nav_subscribe' ) ) {
function woo_nav_subscribe() {
	global $woo_options;
	$class = '';
	if ( isset( $woo_options['woo_header_cart_link'] ) && 'true' == $woo_options['woo_header_cart_link'] )
		$class = ' cart-enabled';

	if ( ( isset( $woo_options['woo_nav_rss'] ) ) && ( $woo_options['woo_nav_rss'] == 'true' ) || ( isset( $woo_options['woo_subscribe_email'] ) ) && ( $woo_options['woo_subscribe_email'] ) ) { ?>
	<ul class="rss fr<?php echo $class; ?>">
		<?php if ( ( isset( $woo_options['woo_subscribe_email'] ) ) && ( $woo_options['woo_subscribe_email'] ) ) { ?>
		<li class="sub-email"><a href="<?php echo esc_url( $woo_options['woo_subscribe_email'] ); ?>"></a></li>
		<?php } ?>
		<?php if ( isset( $woo_options['woo_nav_rss'] ) && ( $woo_options['woo_nav_rss'] == 'true' ) ) { ?>
		<li class="sub-rss"><a href="<?php if ( isset($woo_options['woo_feed_url']) ) { echo esc_url( $woo_options['woo_feed_url'] ); } else { echo esc_url( get_bloginfo_rss( 'rss2_url' ) ); } ?>"></a></li>
		<?php } ?>
	</ul>
	<?php }
} // End woo_nav_subscribe()
}

/*-----------------------------------------------------------------------------------*/
/* Add Search to the navigation bar */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_nav_search' ) ) {
function woo_nav_search() {
	global $woo_options;
?>
	<?php if ( apply_filters( 'woo_nav_search', true ) && ( isset( $woo_options['woo_nav_search'] ) && 'true' == $woo_options['woo_nav_search'] ) ) { ?>
	<ul class="nav-search">
		<li>
			<a class="search-contents" href="#"></a>
			<ul>
				<li>
					<?php
						$args = array(
							'title' => ''
						);

						if ( is_woocommerce_activated() && isset( $woo_options['woo_header_search_scope'] ) && 'products' == $woo_options['woo_header_search_scope'] ) {
							the_widget( 'WC_Widget_Product_Search', $args );
						} else {
							the_widget( 'WP_Widget_Search', $args );
						}
					?>
				</li>
			</ul>
		</li>
	</ul>
	<?php } ?>
<?php
} // End woo_nav_search
}

/*-----------------------------------------------------------------------------------*/
/* Post More  */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_post_more' ) ) {
function woo_post_more() {
	if ( get_option( 'woo_disable_post_more' ) != 'true' ) {

	$html = '';

	if ( get_option('woo_post_content') == 'excerpt' ) { $html .= '[view_full_article] '; }

	$comm = get_option('woo_comments');
	if ( 'post' == $comm || 'both' == $comm ) {
		$html .= '[post_comments]';
	}

	$html = apply_filters( 'woo_post_more', $html );

		if ( $html != '' ) {
?>
	<div class="post-more">
		<?php
			echo $html;
		?>
	</div>
<?php
		}
	}
} // End woo_post_more()
}

/*-----------------------------------------------------------------------------------*/
/* Video Embed  */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'canvas_get_embed' ) ) {
function canvas_get_embed() {
	// Setup height & width of embed
	$width = '610';
	$height = '343';
	$embed = woo_embed( 'width=' . $width . '&height=' . $height );
	if ( '' != $embed ) {
?>
<div class="post-embed">
	<?php echo $embed; ?>
</div><!-- /.post-embed -->
<?php
	}
} // End canvas_get_embed()
}


/*-----------------------------------------------------------------------------------*/
/* Author Box */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_author' ) ) {
function woo_author () {
	// Author box single post page
	if ( is_single() && get_option( 'woo_disable_post_author' ) != 'true' ) { add_action( 'woo_post_inside_after', 'woo_author_box', 10 ); }
	// Author box author page
	if ( is_author() ) { add_action( 'woo_loop_before', 'woo_author_box', 10 ); }
} // End woo_author()
}


/*-----------------------------------------------------------------------------------*/
/* Single Post Author */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_author_box' ) ) {
function woo_author_box () {
	global $post;
	$author_id=$post->post_author;

	// Adjust the arrow, if is_rtl().
	$arrow = '&rarr;';
	if ( is_rtl() ) $arrow = '&larr;';
?>
<aside id="post-author">
	<div class="profile-image"><?php echo get_avatar( $author_id, '80' ); ?></div>
	<div class="profile-content">
		<h4><?php printf( esc_attr__( 'About %s', 'woothemes' ), get_the_author_meta( 'display_name', $author_id ) ); ?></h4>
		<?php echo get_the_author_meta( 'description', $author_id ); ?>
		<?php if ( is_singular() ) { ?>
		<div class="profile-link">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ); ?>">
				<?php printf( __( 'View all posts by %s %s', 'woothemes' ), get_the_author_meta( 'display_name', $author_id ), '<span class="meta-nav">' . $arrow . '</span>' ); ?>
			</a>
		</div><!--#profile-link-->
		<?php } ?>
	</div>
	<div class="fix"></div>
</aside>
<?php
} // End woo_author_box()
}


/*-----------------------------------------------------------------------------------*/
/* Yoast Breadcrumbs */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( '_dep_woo_breadcrumbs' ) ) {
function _dep_woo_breadcrumbs() {
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div id="breadcrumb"><p>', '</p></div>' );
	}
} // End _dep_woo_breadcrumbs()
}


/*-----------------------------------------------------------------------------------*/
/* Subscribe & Connect  */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_subscribe_connect_action' ) ) {
function woo_subscribe_connect_action() {
	if ( is_single() && 'true' == get_option( 'woo_connect' ) ) { add_action('woo_post_inside_after', 'woo_subscribe_connect'); }
} // End woo_subscribe_connect_action()
}


/*-----------------------------------------------------------------------------------*/
/* Optional Top Navigation (WP Menus)  */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_top_navigation' ) ) {
function woo_top_navigation() {
	if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) {
?>
	<div id="top">
		<div class="col-full">
			<?php
				echo '<h3 class="top-menu">' . woo_get_menu_name( 'top-menu' ) . '</h3>';
				wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav top-navigation fl', 'theme_location' => 'top-menu' ) );
			?>
		</div>
	</div><!-- /#top -->
<?php
	}
} // End woo_top_navigation()
}

/*-----------------------------------------------------------------------------------*/
/* Footer Widgetized Areas  */
/*-----------------------------------------------------------------------------------*/

add_action( 'woo_footer_top', 'woo_footer_sidebars', 30 );

if ( ! function_exists( 'woo_footer_sidebars' ) ) {
function woo_footer_sidebars() {
	$settings = woo_get_dynamic_values( array( 'biz_disable_footer_widgets' => '', 'footer_sidebars' => '4' ) );

	$footer_sidebar_total = 4;
	$has_footer_sidebars = false;

	// Check if we have footer sidebars to display.
	for ( $i = 1; $i <= $footer_sidebar_total; $i++ ) {
		if ( woo_active_sidebar( 'footer-' . $i ) && ( $has_footer_sidebars == false ) ) {
			$has_footer_sidebars = true;
		}
	}

	// If footer sidebars are available, we're on the "Business" page template and we want to disable them, do so.
	if ( $has_footer_sidebars && is_page_template( 'template-biz.php' ) && ( 'true' == $settings['biz_disable_footer_widgets'] ) ) {
		$has_footer_sidebars = false;
	}

	$total = $settings['footer_sidebars'];
	if ( '0' == $settings['footer_sidebars'] ) { $total = 0; } // Make sure the footer widgets don't display if the "none" option is set under "Theme Options".

	// Lastly, we display the sidebars.
	if ( $has_footer_sidebars &&  $total > 0 ) {
?>
<section id="footer-widgets" class="col-full col-<?php echo esc_attr( intval( $total ) ); ?>">
	<?php $i = 0; while ( $i < intval( $total ) ) { $i++; ?>
		<?php if ( woo_active_sidebar( 'footer-' . $i ) ) { ?>
	<div class="block footer-widget-<?php echo $i; ?>">
    	<?php woo_sidebar( 'footer-' . $i ); ?>
	</div>
        <?php } ?>
	<?php } // End WHILE Loop ?>
	<div class="fix"></div>
</section><!--/#footer-widgets-->
<?php

	} // End IF Statement
} // End woo_footer_sidebars()
}

/*-----------------------------------------------------------------------------------*/
/* Add customisable footer areas */
/*-----------------------------------------------------------------------------------*/

/**
 * Add customisable footer areas.
 *
 * @package WooFramework
 * @subpackage Actions
 */

if ( ! function_exists( 'woo_footer_left' ) ) {
function woo_footer_left () {
	$settings = woo_get_dynamic_values( array( 'footer_left' => 'true', 'footer_left_text' => '[site_copyright]' ) );

	woo_do_atomic( 'woo_footer_left_before' );

	$html = '';

	if( 'true' == $settings['footer_left'] ) {
		$html .= '<p>' . stripslashes( $settings['footer_left_text'] ) . '</p>';
	} else {
		$html .= '[site_copyright]';
	}

	$html = apply_filters( 'woo_footer_left', $html );

	echo $html;

	woo_do_atomic( 'woo_footer_left_after' );
} // End woo_footer_left()
}

if ( ! function_exists( 'woo_footer_right' ) ) {
function woo_footer_right () {
	$settings = woo_get_dynamic_values( array( 'footer_right' => 'true', 'footer_right_text' => '[site_credit]' ) );

	woo_do_atomic( 'woo_footer_right_before' );

	$html = '';

	if( 'true' == $settings['footer_right'] ) {
		$html .= '<p>' . stripslashes( $settings['footer_right_text'] ) . '</p>';
	} else {
		$html .= '[site_credit]';
	}

	$html = apply_filters( 'woo_footer_right', $html );

	echo $html;

	woo_do_atomic( 'woo_footer_right_after' );
} // End woo_footer_right()
}

/*-----------------------------------------------------------------------------------*/
/* Add customisable post meta */
/*-----------------------------------------------------------------------------------*/

/**
 * Add customisable post meta.
 *
 * Add customisable post meta, using shortcodes,
 * to be added/modified where necessary.
 *
 * @package WooFramework
 * @subpackage Actions
 */

if ( ! function_exists( 'woo_post_meta' ) ) {
function woo_post_meta() {

	if ( is_page() && !( is_page_template( 'template-blog.php' ) || is_page_template( 'template-magazine.php' ) ) ) {
		return;
	}

	$post_info = '<span class="small">' . __( 'By', 'woothemes' ) . '</span> [post_author_posts_link] <span class="small">' . _x( 'on', 'post datetime', 'woothemes' ) . '</span> [post_date] <span class="small">' . __( 'in', 'woothemes' ) . '</span> [post_categories before=""] ';
printf( '<div class="post-meta">%s</div>' . "\n", apply_filters( 'woo_filter_post_meta', $post_info ) );

} // End woo_post_meta()
}

/*-----------------------------------------------------------------------------------*/
/* Add Post Thumbnail to Single posts on Archives */
/*-----------------------------------------------------------------------------------*/

/**
 * Add Post Thumbnail to Single posts on Archives
 *
 * Add code to the woo_post_inside_before() hook.
 *
 * @package WooFramework
 * @subpackage Actions
 */

 add_action( 'woo_post_inside_before', 'woo_display_post_image', 10 );

if ( ! function_exists( 'woo_display_post_image' ) ) {
function woo_display_post_image() {
	$display_image = false;
	$options = woo_get_dynamic_values( array( 'thumb_w' => '100', 'thumb_h' => '100', 'thumb_align' => 'alignleft', 'single_w' => '100', 'single_h' => '100', 'thumb_align_single' => 'alignright', 'thumb_single' => 'false' ) );
	$width = $options['thumb_w'];
	$height = $options['thumb_h'];
	$align = $options['thumb_align'];

	if ( is_single() && ( 'true' == $options['thumb_single'] ) ) {
		$width = $options['single_w'];
		$height = $options['single_h'];
		$align = $options['thumb_align_single'];
		$display_image = true;
	}

	if ( 'true' == get_option( 'woo_woo_tumblog_switch') ) { $is_tumblog = woo_tumblog_test(); } else { $is_tumblog = false; }
	if ( $is_tumblog || ( is_single() && 'false' == $options['thumb_single'] ) ) { $display_image = false; }
	if ( true == $display_image && ! woo_embed( '' ) ) { woo_image( 'width=' . esc_attr( $width ) . '&height=' . esc_attr( $height ) . '&class=thumbnail ' . esc_attr( $align ) ); }
} // End woo_display_post_image()
}

/*-----------------------------------------------------------------------------------*/
/* Post Inside After */
/*-----------------------------------------------------------------------------------*/
/**
 * Post Inside After
 *
 * Add code to the woo_post_inside_after() hook.
 *
 * @package WooFramework
 * @subpackage Actions
 */

 add_action( 'woo_post_inside_after_singular-post', 'woo_post_inside_after_default', 10 );

if ( ! function_exists( 'woo_post_inside_after_default' ) ) {
function woo_post_inside_after_default() {

	$post_info ='[post_tags before=""]';
	printf( '<div class="post-utility">%s</div>' . "\n", apply_filters( 'woo_post_inside_after_default', $post_info ) );

} // End woo_post_inside_after_default()
}

/*-----------------------------------------------------------------------------------*/
/* Modify the default "comment" form field. */
/*-----------------------------------------------------------------------------------*/
/**
 * Modify the default "comment" form field.
 *
 * @package WooFramework
 * @subpackage Filters
 */

  add_filter( 'comment_form_field_comment', 'woo_comment_form_comment', 10 );

if ( ! function_exists( 'woo_comment_form_comment' ) ) {
function woo_comment_form_comment ( $field ) {
	$field = str_replace( '<label ', '<label class="hide" ', $field );
	$field = str_replace( 'cols="45"', 'cols="50"', $field );
	$field = str_replace( 'rows="8"', 'rows="10"', $field );

	return $field;
} // End woo_comment_form_comment()
}

/*-----------------------------------------------------------------------------------*/
/* Add theme default comment form fields. */
/*-----------------------------------------------------------------------------------*/
/**
 * Add theme default comment form fields.
 *
 * @package WooFramework
 * @subpackage Filters
 */

add_filter( 'comment_form_default_fields', 'woo_comment_form_fields', 10 );

if ( ! function_exists( 'woo_comment_form_fields' ) ) {
function woo_comment_form_fields ( $fields ) {
	$commenter = wp_get_current_commenter();

$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );

	$fields =  array(
	'author' => '<p class="comment-form-author"><input id="author" name="author" type="text" class="txt" tabindex="1" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' .
				'<label for="author">' . __( 'Name', 'woothemes' ) . ( $req ? ' <span class="required">(' . __( 'required', 'woothemes' ) . ')</span>' : '' ) . '</label> ' . '</p>',
	'email'  => '<p class="comment-form-email"><input id="email" name="email" type="text" class="txt" tabindex="2" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' .
				'<label for="email">' . __( 'Email (will not be published)', 'woothemes' ) . ( $req ? ' <span class="required">(' . __( 'required', 'woothemes' ) . ')</span>' : '' ) . '</label> ' . '</p>',
	'url'    => '<p class="comment-form-url"><input id="url" name="url" type="text" class="txt" tabindex="3" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
	            '<label for="url">' . __( 'Website', 'woothemes' ) . '</label></p>',
);

	return $fields;
} // End woo_comment_form_fields()
}

/*-----------------------------------------------------------------------------------*/
/* Add theme default comment form arguments. */
/*-----------------------------------------------------------------------------------*/
/**
 * Add theme default comment form arguments.
 *
 * @package WooFramework
 * @subpackage Filters
 */

add_filter( 'comment_form_defaults', 'woo_comment_form_args', 10 );

if ( ! function_exists( 'woo_comment_form_args' ) ) {
	function woo_comment_form_args ( $args ) {
		// Add tabindex of "field count + 1" to the comment textarea. This lets us cater for additional fields and have a dynamic tab index.
		$tabindex = count( $args['fields'] ) + 1;
		$args['comment_field'] = str_replace( '<textarea ', '<textarea tabindex="' . $tabindex . '" ', $args['comment_field'] );

		// Adjust tabindex for "submit" button.
		$tabindex++;

		$args['label_submit'] = __( 'Submit Comment', 'woothemes' );
		$args['comment_notes_before'] = '';
		$args['comment_notes_after'] = '';
		$args['cancel_reply_link'] = __( 'Click here to cancel reply.', 'woothemes' );

		return $args;
	} // End woo_comment_form_args()
}

/*-----------------------------------------------------------------------------------*/
/* Activate shortcode compatibility in our new custom areas. */
/*-----------------------------------------------------------------------------------*/
/**
 * Activate shortcode compatibility in our new custom areas.
 *
 * @package WooFramework
 * @subpackage Filters
 */
 	$sections = array( 'woo_filter_post_meta', 'woo_post_inside_after_default', 'woo_post_more', 'woo_footer_left', 'woo_footer_right' );

 	foreach ( $sections as $s ) { add_filter( $s, 'do_shortcode', 20 ); }

/*-----------------------------------------------------------------------------------*/
/* woo_content_templates_magazine() */
/*-----------------------------------------------------------------------------------*/
/**
 * woo_content_templates_magazine()
 *
 * Remove the tumblog content template from the templates
 * to search through, if on the "Magazine" page template.
 *
 * @package WooFramework
 * @subpackage Filters
 */

add_filter( 'woo_content_templates', 'woo_content_templates_magazine', 10 );

if ( ! function_exists( 'woo_content_templates_magazine' ) ) {
	function woo_content_templates_magazine ( $templates ) {
		global $page_template;

		if ( $page_template == 'template-magazine.php' ) {
			foreach ( $templates as $k => $v ) {
				$v = str_replace( '.php', '', $v );
				$bits = explode( '-', $v );
				if ( $bits[1] == 'tumblog' ) {
					unset( $templates[$k] );
				}
			}
		}

		return $templates;
	} // End woo_content_templates_magazine()
}

/*-----------------------------------------------------------------------------------*/
/* woo_feedburner_link() */
/*-----------------------------------------------------------------------------------*/
/**
 * woo_feedburner_link()
 *
 * Replace the default RSS feed link with the Feedburner URL, if one
 * has been provided by the user.
 *
 * @package WooFramework
 * @subpackage Filters
 */

add_filter( 'feed_link', 'woo_feedburner_link', 10 );

if ( ! function_exists( 'woo_feedburner_link' ) ) {
function woo_feedburner_link ( $output, $feed = null ) {
	global $woo_options;

	$default = get_default_feed();

	if ( ! $feed ) $feed = $default;

	if ( isset( $woo_options['woo_feed_url'] ) && $woo_options['woo_feed_url'] && ( $feed == $default ) && ( ! stristr( $output, 'comments' ) ) ) $output = $woo_options['woo_feed_url'];

	return esc_url( $output );
} // End woo_feedburner_link()
}

/*-----------------------------------------------------------------------------------*/
/* Help WooTumblog to recognise if it's on the "Magazine" page template */
/*-----------------------------------------------------------------------------------*/

add_action( 'get_template_part_content', 'woo_magazine_adjust_tumblog_widths', 2, 10 );

/**
 * woo_magazine_adjust_tumblog_widths function.
 *
 * @access public
 * @param string $slug
 * @param string $name
 * @return void
 */
if ( ! function_exists( 'woo_magazine_adjust_tumblog_widths' ) ) {
function woo_magazine_adjust_tumblog_widths ( $slug, $name ) {
	if ( $name == 'magazine-grid' ) {
		woo_magazine_apply_tumblog_width_adjustments();
	}
} // End woo_magazine_adjust_tumblog_widths()
}

/**
 * woo_magazine_apply_tumblog_width_adjustments function.
 *
 * @access public
 * @return void
 */
if ( ! function_exists( 'woo_magazine_apply_tumblog_width_adjustments' ) ) {
function woo_magazine_apply_tumblog_width_adjustments () {
	add_filter( 'option_woo_tumblog_image_width', 'woo_magazine_tumblog_adjust_width_grid', 10 );
	add_filter( 'option_woo_tumblog_video_width', 'woo_magazine_tumblog_adjust_width_grid', 10 );
	add_filter( 'option_woo_tumblog_audio_width', 'woo_magazine_tumblog_adjust_width_grid', 10 );
} // End woo_magazine_apply_tumblog_width_adjustments()
}

/**
 * woo_magazine_tumblog_adjust_width_grid function.
 *
 * @access public
 * @param string $width
 * @return int $width
 */
if ( ! function_exists( 'woo_magazine_tumblog_adjust_width_grid' ) ) {
function woo_magazine_tumblog_adjust_width_grid ( $width ) {
	return woo_magazine_determine_tumblog_width( current_filter() );
} // End woo_magazine_tumblog_adjust_width_grid()
}

/**
 * woo_magazine_determine_tumblog_width function.
 *
 * @access public
 * @param string $filter
 * @return int $width
 */
if ( ! function_exists( 'woo_magazine_determine_tumblog_width' ) ) {
function woo_magazine_determine_tumblog_width ( $filter ) {
	global $woo_options;
	$width = 300;

	if ( isset( $woo_options['woo_tumblog_magazine_media_width'] ) && ( $woo_options['woo_tumblog_magazine_media_width'] != '' ) ) {
		$width = $woo_options['woo_tumblog_magazine_media_width'];
	}

	return apply_filters( 'woo_magazine_tumblog_width', $width, $filter );
} // End woo_magazine_determine_tumblog_width()
}

/*-----------------------------------------------------------------------------------*/
/* Enqueue dynamic CSS */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_enqueue_custom_styling' ) ) {
function woo_enqueue_custom_styling () {
	echo "\n" . '<!-- Custom CSS Styling -->' . "\n";
	echo '<style type="text/css">' . "\n";
	woo_custom_styling();
	echo '</style>' . "\n";
} // End woo_enqueue_custom_styling()
}

/*-----------------------------------------------------------------------------------*/
/* Load site width CSS in the header */
/*-----------------------------------------------------------------------------------*/

add_action( 'wp_head', 'woo_load_site_width_css', 9 );

if ( ! function_exists( 'woo_load_site_width_css' ) ) {
function woo_load_site_width_css () {
	$settings = woo_get_dynamic_values( array( 'layout_width' => 960 ) );
    $layout_width = intval( $settings['layout_width'] );
    if ( 0 < $layout_width && 960 != $layout_width ) { /* Has legitimate width */ } else { return; } // Use default width from stylesheet
?>

<!-- Adjust the website width -->
<style type="text/css">
	.col-full, #wrapper { max-width: <?php echo intval( $layout_width ); ?>px !important; }
</style>

<?php
} // End woo_load_site_width_css()
}

/*-----------------------------------------------------------------------------------*/
/* Function to optionally remove responsive design and load in fallback CSS styling. */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_remove_responsive_design' ) ) {
/**
 * Trigger items for removing responsive design from Canvas.
 * @since  5.0.13
 * @return void
 */
function woo_remove_responsive_design () {
	remove_action( 'wp_head', 'woo_load_site_width_css', 9 );
	// Load in CSS file for non-responsive layouts.
	wp_enqueue_style( 'non-responsive' );
	// Load non-responsive site width CSS.
	add_action( 'wp_print_scripts', 'woo_load_site_width_css_nomedia', 10 );
	// Remove mobile viewport scale meta tag
	remove_action( 'wp_head', 'woo_load_responsive_meta_tags', 1 );
} // End woo_remove_responsive_design()
}

if ( ! function_exists( 'woo_load_site_width_css_nomedia' ) ) {
/**
 * Load the layout width CSS without a media query wrapping it.
 * @since  5.0.13
 * @return void
 */
function woo_load_site_width_css_nomedia () {
	$settings = woo_get_dynamic_values( array( 'layout_width' => 960 ) );
    $layout_width = intval( $settings['layout_width'] );
    if ( 0 < $layout_width ) { /* Has legitimate width */ } else { $layout_width = 960; } // Default Width
?>
<style type="text/css">.col-full, #wrapper { width: <?php echo intval( $layout_width ); ?>px; max-width: <?php echo intval( $layout_width ); ?>px; } #inner-wrapper { padding: 0; } body.full-width #header, #nav-container, body.full-width #content, body.full-width #footer-widgets, body.full-width #footer { padding-left: 0; padding-right: 0; } body.fixed-mobile #top, body.fixed-mobile #header-container, body.fixed-mobile #footer-container, body.fixed-mobile #nav-container, body.fixed-mobile #footer-widgets-container { min-width: <?php echo intval( $layout_width ); ?>px; padding: 0 1em; } body.full-width #content { width: auto; padding: 0 1em;}</style>
<?php
} // End woo_load_site_width_css_nomedia()
}

if ( ! function_exists( 'woo_load_responsive_design_removal' ) ) {
/**
 * Trigger the removal of the responsive design in Canvas. Must be hooked onto "init".
 * @since  5.0.13
 * @uses  woo_remove_responsive_design()
 * @return void
 */
function woo_load_responsive_design_removal () {
	add_action( 'wp_print_styles', 'woo_remove_responsive_design', 10 );
} // End woo_load_responsive_design_removal()
}

/*-----------------------------------------------------------------------------------*/
/* Load non-responsive.css for IE8 */
/*-----------------------------------------------------------------------------------*/

add_action( 'wp_head', 'woo_load_non_responsive_css', 8 );

if ( ! function_exists( 'woo_load_non_responsive_css' ) ) {
	function woo_load_non_responsive_css() {
		// Load conditional CSS for IE8
		echo '<!--[if lt IE 9]>'. "\n";
		echo '<link href="'. esc_url( get_template_directory_uri() . '/css/non-responsive.css' ) . '" rel="stylesheet" type="text/css" />' . "\n";
		// Load the site width in addition to max-width to make it fixed
		woo_load_site_width_css_nomedia();
		echo '<![endif]-->'. "\n";
	}
}

/*-----------------------------------------------------------------------------------*/
/* Adjust the homepage query, if using the "Magazine" page template as the homepage. */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_modify_magazine_homepage_query' ) ) {
function woo_modify_magazine_homepage_query ( $q ) {
	if ( ! is_admin() && $q->is_main_query() && ( 0 < $q->query_vars['page_id'] ) && ( $q->query_vars['page_id'] == get_option( 'page_on_front' ) ) && ( 'template-magazine.php' == get_post_meta( intval( $q->query_vars['page_id'] ), '_wp_page_template', true ) ) ) {

		$settings = woo_get_dynamic_values( array( 'magazine_limit' => get_option( 'posts_per_page' ) ) );

		$q->set( 'posts_per_page', intval( $settings['magazine_limit'] ) );

		if ( isset( $q->query_vars['page'] ) ) {
			$q->set( 'paged', intval( $q->query_vars['page'] ) );
		}

		$q->parse_query();
	}
	return $q;
} // End woo_modify_magazine_homepage_query()
}

add_filter( 'pre_get_posts', 'woo_modify_magazine_homepage_query' );

/*-----------------------------------------------------------------------------------*/
/* WooTumblog Loader. */
/*-----------------------------------------------------------------------------------*/

if ( get_option( 'woo_woo_tumblog_switch' ) == 'true' ) {
	$includes_path = get_template_directory() . '/includes/';
	define( 'WOOTUMBLOG_ACTIVE', true ); // Define a constant for use in our theme's templating engine.
	require_once ( $includes_path . 'tumblog/theme-tumblog.php' );		// Tumblog Output Functions
	// Test for Post Formats
	if ( get_option( 'woo_tumblog_content_method' ) == 'post_format' ) {
		require_once( $includes_path . 'tumblog/wootumblog_postformat.class.php' );
	} else {
		require_once ( $includes_path . 'tumblog/theme-custom-post-types.php' );	// Custom Post Types and Taxonomies
	}

	// Test for Post Formats
	if ( get_option( 'woo_tumblog_content_method' ) == 'post_format' ) {
	    global $woo_tumblog_post_format;
	    $woo_tumblog_post_format = new WooTumblogPostFormat();
	    if ( $woo_tumblog_post_format->woo_tumblog_upgrade_existing_taxonomy_posts_to_post_formats()) {
	    	update_option( 'woo_tumblog_post_formats_upgraded', 'true' );
	    }
	}

	// Show in RSS feed
	if ( get_option( 'woo_custom_rss' ) == 'true' ) {
		add_filter( 'the_excerpt_rss', 'woo_custom_tumblog_rss_output' );
		add_filter( 'the_content_rss', 'woo_custom_tumblog_rss_output' );
	}
}

/*-----------------------------------------------------------------------------------*/
/* Full width header */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_full_width_header' ) ) {
function woo_full_width_header() {
	$settings = woo_get_dynamic_values( array( 'header_full_width' => '', 'layout_boxed' => '' ) );

	if ( 'true' == $settings['layout_boxed'] ) return;
	if ( 'true' != $settings['header_full_width'] ) return;


	// Add header container
	add_action( 'woo_header_before', 'woo_header_container_start' );
	add_action( 'woo_header_after', 'woo_header_container_end', 8 );

	// Add navigation container
	add_action( 'woo_nav_before', 'woo_nav_container_start' );
	add_action( 'woo_nav_after', 'woo_nav_container_end' );
} // End woo_full_width_header()
}

add_action( 'get_header', 'woo_full_width_header', 10 );

/*-----------------------------------------------------------------------------------*/
/* Full width footer */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_full_width_footer' ) ) {
function woo_full_width_footer() {
	$settings = woo_get_dynamic_values( array( 'footer_full_width' => '', 'layout_boxed' => '' ) );

	if ( 'true' == $settings['layout_boxed'] ) return;
	if ( 'true' != $settings['footer_full_width'] ) return;

	// Add footer widget container
	add_action( 'woo_footer_top', 'woo_footer_widgets_container_start', 8 );
	add_action( 'woo_footer_before', 'woo_footer_widgets_container_end' );

	// Add footer container
	add_action( 'woo_footer_before', 'woo_footer_container_start' );
	add_action( 'woo_footer_after', 'woo_footer_container_end' );
} // End woo_full_width_footer()
}

add_action( 'get_header', 'woo_full_width_footer', 10 );

/*-----------------------------------------------------------------------------------*/
/* Full Width Markup Functions */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_header_container_start' ) ) {
function woo_header_container_start () {
?>
    <!--#header-container-->
    <div id="header-container">
<?php
} // End woo_header_container_start()
}

if ( ! function_exists( 'woo_header_container_end' ) ) {
function woo_header_container_end () {
?>
    </div><!--/#header-container-->
<?php
} // End woo_header_container_end()
}

if ( ! function_exists( 'woo_nav_container_start' ) ) {
function woo_nav_container_start () {
?>
    <!--#nav-container-->
    <div id="nav-container">
<?php
} // End woo_nav_container_start()
}

if ( ! function_exists( 'woo_nav_container_end' ) ) {
function woo_nav_container_end () {
?>
    </div><!--/#nav-container-->
<?php
} // End woo_nav_container_end()
}

if ( ! function_exists( 'woo_footer_widgets_container_start' ) ) {
function woo_footer_widgets_container_start () {
?>
    <!--#footer-widgets-container-->
    <div id="footer-widgets-container">
<?php
} // End woo_footer_widgets_container_start()
}

if ( ! function_exists( 'woo_footer_widgets_container_end' ) ) {
function woo_footer_widgets_container_end () {
?>
	</div><!--/#footer_widgets_container_end-->
<?php
}
}

if ( ! function_exists( 'woo_footer_container_start' ) ) {
function woo_footer_container_start () { ?>
    <!--#footer_container_start-->
    <div id="footer-container">
<?php
} // End woo_footer_container_start()
}

if ( ! function_exists( 'woo_footer_container_end' ) ) {
function woo_footer_container_end () { ?>
    </div><!--/#footer_container_end-->
<?php
} // End woo_footer_container_end()
}

/*-----------------------------------------------------------------------------------*/
/* Full width body classes */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_add_full_width_class' ) ) {
function woo_add_full_width_class ( $classes ) {
	$settings = woo_get_dynamic_values( array( 'header_full_width' => 'false', 'footer_full_width' => 'false', 'layout_boxed' => '', 'slider_biz_full' => 'false' ) );
	if ( 'true' == $settings['layout_boxed'] ) return $classes; // Don't add the full width CSS classes if boxed layout is enabled.

	if ( 'true' == $settings['header_full_width'] || 'true' == $settings['footer_full_width'] ) {

		$classes[] = 'full-width';

		if ( 'true' == $settings['header_full_width'] ) {
			$classes[] = 'full-header';
		}

		if ( 'true' == $settings['footer_full_width'] ) {
			$classes[] = 'full-footer';
		}
	}

	if ( 'true' == $settings['slider_biz_full'] && is_page_template( 'template-biz.php' ) ) {
		if ( !in_array( 'full-width', $classes ) ) {
			$classes[] = 'full-width';
		}

		$classes[] = 'full-slider';
	}

	return $classes;
} // End woo_add_full_width_class()
}

add_filter( 'body_class', 'woo_add_full_width_class', 10 );


/*-----------------------------------------------------------------------------------*/
/* Optionally load custom logo. */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_logo' ) ) {
function woo_logo () {
	$settings = woo_get_dynamic_values( array( 'logo' => '' ) );
	// Setup the tag to be used for the header area (`h1` on the front page and `span` on all others).
	$heading_tag = 'span';
	if ( is_home() || is_front_page() ) { $heading_tag = 'h1'; }

	// Get our website's name, description and URL. We use them several times below so lets get them once.
	$site_title = get_bloginfo( 'name' );
	$site_url = home_url( '/' );
	$site_description = get_bloginfo( 'description' );
?>
<div id="logo">
<?php
	// Website heading/logo and description text.
	$logo_url = apply_filters( 'woo_logo_img', $settings['logo'] );
	if ( ( '' != $logo_url ) ) {
		if ( is_ssl() ) $logo_url = str_replace( 'http://', 'https://', $logo_url );

		echo '<a href="' . esc_url( $site_url ) . '" title="' . esc_attr( $site_description ) . '"><img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $site_title ) . '" /></a>' . "\n";
	} // End IF Statement

	echo '<' . $heading_tag . ' class="site-title"><a href="' . esc_url( $site_url ) . '">' . $site_title . '</a></' . $heading_tag . '>' . "\n";
	if ( $site_description ) { echo '<span class="site-description">' . $site_description . '</span>' . "\n"; }
?>
</div>
<?php
} // End woo_logo()
}

add_action( 'woo_header_inside', 'woo_logo', 10 );

/*-----------------------------------------------------------------------------------*/
/* Optionally load the mobile navigation toggle. */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_nav_toggle' ) ) {
function woo_nav_toggle () {
?>
<h3 class="nav-toggle icon"><a href="#navigation"><?php _e( 'Navigation', 'woothemes' ); ?></a></h3>
<?php
} // End woo_nav_toggle()
}

add_action( 'woo_header_before', 'woo_nav_toggle', 20 );

/*-----------------------------------------------------------------------------------*/
/* Widgetized header area */
/*-----------------------------------------------------------------------------------*/

// Add the code inside the header area
add_action( 'woo_header_inside', 'woo_header_widgetized' );
if ( ! function_exists( 'woo_header_widgetized' ) ) {
	function woo_header_widgetized() {
	    if ( woo_active_sidebar( 'header' ) ) {
	?>
	    <div class="header-widget">
	        <?php woo_sidebar( 'header' ) ?>
	    </div>
	<?php
	    }
	}
}

/*-----------------------------------------------------------------------------------*/
/* Show page content on portfolio page */
/*-----------------------------------------------------------------------------------*/

add_action( 'woo_loop_before', 'woo_portfolio_page_content' );
if ( ! function_exists( 'woo_portfolio_page_content' ) ) {
	function woo_portfolio_page_content() {
		if ( ! is_page_template( 'template-portfolio.php' ) ) { return; }
		// Show page content first
		if (have_posts()) {
			the_post();
			woo_get_template_part( 'content', 'page-template-business' ); // Use business content so we don't output a page title
		}
	}
}

/*-----------------------------------------------------------------------------------*/
/* END */
/*-----------------------------------------------------------------------------------*/
?>