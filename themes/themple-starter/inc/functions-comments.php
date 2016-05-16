<?php

// Functions used by comments


// Custom Comment output
function tpl_comment( $comment, $args, $depth ) {
	$GLOBALS["comment"] = $comment;
	extract( $args, EXTR_SKIP );

	$tag = 'article';
	$add_below = 'comment';

	?>
	<<?php echo $tag ?> <?php comment_class( empty( $args["has_children"] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID(); ?>" itemscope itemtype="http://schema.org/Comment">
		<div class="comment-inner">
			<figure class="gravatar"><?php echo get_avatar( $comment, $args["avatar_size"] ); ?></figure>
			<div class="comment-meta" role="complementary">
				<p class="comment-author"><?php _e( 'Posted on', 'themple-starter' ); ?> <time class="comment-meta-item" datetime="<?php comment_date( 'Y-m-d' ); ?>T<?php comment_time( 'H:iP' ); ?>" itemprop="datePublished"><?php comment_date( 'jS F Y' ); ?>, <?php comment_time(); ?></time> <?php
					if ( get_comment_author_url() != '' ) {
						$cauthor_text = '<a class="comment-author-link" href="'. get_comment_author_url() .'" itemprop="author">' . get_comment_author() . '</a>';
					}
					else {
						$cauthor_text = get_comment_author();
					}
					printf( __( 'by %s', 'themple-starter' ), $cauthor_text );
					?></p>
				<div class="comment-reply">
					<?php $comment_id = null;
					if ( $depth == $args["max_depth"] ) {
						$comment_id = $comment->comment_parent;
						$depth--;
					}
					comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args["max_depth"] ) ), $comment_id ); ?>
					<?php edit_comment_link( '<small>' . __( 'Edit this comment', 'themple-starter' ) . '</small>' ); ?>
				</div>
				<?php if ( $comment->comment_approved == '0' ) { ?>
				<p class="comment-meta-item"><?php _e( 'Your comment is awaiting moderation.', 'themple-starter' ); ?></p>
				<?php } ?>
			</div>
			<div class="comment-content" itemprop="text">
				<?php comment_text(); ?>
			</div>
		</div>
	<?php
}



// End of comment
function tpl_comment_end() {
	echo '</article>';
}
