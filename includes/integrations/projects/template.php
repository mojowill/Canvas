<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Before Content
 * Wraps all projects content in wrappers which match the theme markup
 * @since   1.0.0
 * @return  void
 * @uses  	woo_content_before(), woo_main_before()
 */
if ( ! function_exists( 'woo_projects_before_content' ) ) {
	function woo_projects_before_content() {
		?>
		<!-- #content Starts -->
		<?php woo_content_before(); ?>
	    <div id="content" class="col-full">

	    	<div id="main-sidebar-container">

				<!-- #main Starts -->
				<?php woo_main_before(); ?>
				<div id="main" class="col-left">

	    <?php
	} // End woo_projects_before_content()
}

/**
 * After Content
 * Closes the wrapping divs
 * @since   1.0.0
 * @return  void
 * @uses    woo_main_after(), do_action(), woo_content_after()
 */
if ( ! function_exists( 'woo_projects_after_content' ) ) {
	function woo_projects_after_content() {
		?>

				</div><!-- /#main -->
		        <?php woo_main_after(); ?>

		        <?php do_action( 'projects_sidebar' ); ?>

	        </div><!-- /#main-sidebar-container -->

	    </div><!-- /#content -->
		<?php woo_content_after(); ?>
	    <?php
	} // End woo_projects_after_content()
}

/**
 * Single Project Title
 * Replaces h1 with h2
 * @since   1.0.0
 * @return  void
 * @uses    do_action()
 */
if ( ! function_exists( 'woo_projects_template_single_title' ) ) {
	function woo_projects_template_single_title() {
		?>
			<h2 itemprop="name" class="project_title entry-title"><?php the_title(); ?></h2>
		<?php
	}
}

/**
 * Before Single Project Content
 * Opens the .entry wrapping div
 * @since   1.0.0
 * @return  void
 * @uses    do_action()
 */
if ( ! function_exists( 'woo_projects_before_summary' ) ) {
	function woo_projects_before_summary() {
		?>
			<div class="entry">
		<?php
	}
}

/**
 * After Single Project Content
 * Closes the .entry wrapping div
 * @since   1.0.0
 * @return  void
 * @uses    do_action()
 */
if ( ! function_exists( 'woo_projects_after_summary' ) ) {
	function woo_projects_after_summary() {
		?>
			</div><!-- /.entry -->
		<?php
	}
}

/**
 * Before Single Project Media
 * Opens the .project-media wrapping div
 * @since   1.0.0
 * @return  void
 * @uses    do_action()
 */
if ( ! function_exists( 'woo_projects_before_media' ) ) {
	function woo_projects_before_media() {
		?>
			<div class="entry-media">
		<?php
	}
}

/**
 * After Single Project Media
 * Closes the .project-media wrapping div
 * @since   1.0.0
 * @return  void
 * @uses    do_action()
 */
if ( ! function_exists( 'woo_projects_after_media' ) ) {
	function woo_projects_after_media() {
		?>
			</div><!-- /.entry-media -->
		<?php
	}
}

/**
 * Loop columns number
 * Changes default value from 2 to 4
 * @since   1.0.0
 * @return  void
 * @uses    do_action()
 */
if ( ! function_exists( 'woo_custom_projects_loop_columns' ) ) {
	function woo_custom_projects_loop_columns( $columns ) {
		global $woo_options;
		if ( 'one-col' == woo_get_layout() ) {
			$columns = 4;
		} else {
			$columns = 2;
		}
		return $columns;
	}
}

/**
 * Projects Pagination
 * Replaces Projects pagination with the function in the WooFramework
 * @uses  woo_projects_add_search_fragment()
 * @uses  woo_projects_pagination_defaults()
 * @uses  woo_pagination()
 */
if ( ! function_exists( 'woo_projects_pagination' ) ) {
	function woo_projects_pagination() {
		if ( is_search() && is_post_type_archive() ) {
			add_filter( 'woo_pagination_args', 			'woo_projects_add_search_fragment', 10 );
			add_filter( 'woo_pagination_args_defaults', 'woo_projects_pagination_defaults', 10 );
		}
		woo_pagination();
	} // End woo_projects_pagination()
}

/**
 * Search Fragment
 * @param  array $settings Fragments
 * @return array           Fragments
 */
if ( ! function_exists( 'woo_projects_add_search_fragment' ) ) {
	function woo_projects_add_search_fragment ( $settings ) {
		$settings['add_fragment'] = '&post_type=product';
		return $settings;
	} // End woo_projects_add_search_fragment()
}

/**
 * Pagination Defaults
 * @param  array $settings Settings
 * @return array           Settings
*/
if ( ! function_exists( 'woo_projects_pagination_defaults' ) ) {
	function woo_projects_pagination_defaults ( $settings ) {
		$settings['use_search_permastruct'] = false;
		return $settings;
	} // End woo_projects_pagination_defaults()
}

/**
 * Breadcrumbs
 * @uses  woo_breadcrumbs()
*/
if ( ! function_exists( 'woo_projects_breadcrumbs' ) ) {
	function woo_projects_breadcrumbs () {
		global $woo_options;
		if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) {
			woo_breadcrumbs();
		}
	} // End woo_projects_breadcrumbs()
}

/**
 * Category Navigation
 * @since   5.9.0
*/
if ( ! function_exists( 'woo_projects_category_nav' ) ) {
	function woo_projects_category_nav () {
		$settings = woo_get_dynamic_values( array( 'projects_old_look' => 'false' ) );
		if ( 'false' == $settings['projects_old_look'] ) return;
?>
	<div class="projects-category-nav">
		<?php
			$args = array( 'hide_empty=0' );
			$terms = get_terms( 'project-category', $args );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			    $count = count( $terms );
			    $term_list .= '<span class="select-category">' . __( 'Select a category:', 'woothemes' ) . '</span>';
			    $term_list .= '<ul>';
			    if ( is_post_type_archive( 'project' ) ) { $class = 'class="current" '; }
			    $term_list .= '<li><a ' . $class . 'href="' . esc_url( get_post_type_archive_link('project') ) . '" title="' . __( 'View all projects', 'woothemes' ) . '">' . __( 'All', 'woothemes' ) . '</a></li>';
			    foreach ( $terms as $term ) {
					if ( is_tax( 'project-category', $term ) ) {
						$class = 'class="current" ';
					} else {
						$class = '';
					}
			    	$term_list .= '<li><a ' . $class . 'href="' . esc_url( get_term_link( $term ) ) . '" title="' . sprintf( __( 'View all post filed under %s', 'woothemes' ), esc_attr( $term->name ) ) . '">' . esc_attr( $term->name ) . '</a></li>';
			    }
				$term_list .= '</ul>';
			    echo $term_list;
			}
		?>
	</div>
<?php
	} // End woo_projects_breadcrumbs()
}