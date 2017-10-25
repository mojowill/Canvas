<?php
// Fist full of comments
if (!function_exists( 'custom_comment' ) ) {
	function custom_comment( $comment, $args, $depth ) {
	   $GLOBALS['comment'] = $comment; ?>

		<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>

	      	<div id="li-comment-<?php comment_ID(); ?>" class="comment-container">

				<?php if( 'comment' == get_comment_type() ) { ?>
	                <div class="avatar"><?php the_commenter_avatar( $args ); ?></div>
	            <?php } ?>

		      	<div class="comment-head">

	                <span class="name"><?php comment_author_link(); ?></span>
	                <span class="date"><?php echo get_comment_date( get_option( 'date_format' ) ); ?> <?php _e( 'at', 'woothemes' ); ?> <?php echo get_comment_time( get_option( 'time_format' ) ); ?></span>
	                <span class="perma"><a href="<?php echo esc_url( get_comment_link() ); ?>" title="<?php esc_attr_e( 'Direct link to this comment', 'woothemes' ); ?>">#</a></span>
	                <span class="edit"><?php edit_comment_link( __( 'Edit', 'woothemes' ), '', '' ); ?></span>

				</div><!-- /.comment-head -->

		   		<div class="comment-entry">

				<?php comment_text(); ?>

				<?php if ( '0' == $comment->comment_approved) { ?>
	                <p class='unapproved'><?php _e( 'Your comment is awaiting moderation.', 'woothemes' ); ?></p>
	            <?php } ?>

	                <div class="reply">
	                    <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
	                </div><!-- /.reply -->

				</div><!-- /comment-entry -->

			</div><!-- /.comment-container -->

	<?php
	}
}

// PINGBACK / TRACKBACK OUTPUT
if ( ! function_exists( 'list_pings' ) ) {
	function list_pings( $comment, $args, $depth ) {

		$GLOBALS['comment'] = $comment;
?>
		<li id="comment-<?php comment_ID(); ?>">
			<span class="author"><?php comment_author_link(); ?></span> -
			<span class="date"><?php echo get_comment_date( get_option( 'date_format' ) ); ?></span>
			<span class="pingcontent"><?php comment_text(); ?></span>
<?php
	}
}

if ( ! function_exists( 'the_commenter_avatar' ) ) {
	function the_commenter_avatar( $args ) {
		global $comment;
	    $avatar = get_avatar( $comment,  $args['avatar_size'] );
	    echo $avatar;
	}
}
?>