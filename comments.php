<?php
if ( post_password_required() ) {
	return;
}
if ( have_comments() ) {
	echo '<hr>';
}
?>
<section id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'themple-starter' ),
					number_format_i18n( get_comments_number() ), get_the_title() );
			?>
		</h2>

		<div class="comment-list">
			<?php
			wp_list_comments( array(
				'avatar_size'	=> 64,
				'max_depth'		=> 4,
				'reply_text'	=> __( 'Reply to this', 'themple-starter' ),
				'callback'		=> 'tpl_comment',
				'end-callback'	=> 'tpl_comment_end',
				'style'			=> 'div',
			) );
			?>
		</div>
		<div class="comment-pagination">
			<?php paginate_comments_links(); ?>
		</div>

	<?php endif; ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note
		if ( !comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'themple-starter' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</section>
