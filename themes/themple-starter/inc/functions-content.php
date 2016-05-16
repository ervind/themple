<?php

// Functions for content


// Replaces the excerpt "more" text with a link
function tpl_excerpt_more( $more ) {

	global $post;

	return ' <a class="readmore" href="'. get_permalink( $post->ID ) . '">'. esc_html( tpl_get_value ( 'readmore' ) ) .'</a>';

}
add_filter( 'excerpt_more', 'tpl_excerpt_more' );



// Get the layout using the theme's layout hierarchy. Used by the template files
function tpl_get_layout() {

	// If not singular page, use the global layout
	if ( !is_singular() ) {

		$layout = tpl_get_value( 'index_layout' );
		if ( $layout == false ) {
			$layout = 'right';
		}

	}

	// If singular, use the local setting
	else {

		$local_layout = tpl_get_value( 'local_layout' );
		if ( $local_layout != false && $local_layout != 'inherit' ) {
			$layout = $local_layout;
		}
		// if no local setting present (or is inheritent value), use the global settings
		else {
			$layout = tpl_get_value( 'general_post_layout' );
			if ( $layout == false ) {
				$layout = tpl_get_value( 'index_layout' );
			}
			if ( $layout == false ) {
				// By default, use the right sidebar layout
				$layout = 'right';
			}
		}

	}

	return $layout;

}


// Shows the formatted or unformatted version of the post in the loop. $post is the current post object, $format is how it should be formatted. This funtion is connected with the posts' 'excerpt_format' option
function tpl_show_post_in_loop( $post, $format = 'default' ) {

	// If unformatted, the excerpt is returned without html elements
	if ( $format == 'unformatted' ) {
		the_excerpt();
		return;
	}

	// If formatted, some html elements are enabled in the loop's excerpts
	if ( $format == 'formatted' ) {
		if ( strpos( $post->post_content, '<!--more-->' ) ) {
			the_content( esc_html( tpl_get_value( 'readmore' ) ) );
		}
		else {
			echo tpl_trim_excerpt( get_the_content() );
		}
		return;
	}

	// Full: show the full article formatted in the loop
	if ( $format == 'full' ) {
		the_content( esc_html( tpl_get_value( 'readmore' ) ) );
		return;
	}

	// Default: show formatted version when using the more tag, but the excerpt if not
	if ( strpos( $post->post_content, '<!--more-->' ) ) {
		the_content( esc_html( tpl_get_value( 'readmore' ) ) );
	}
	else {
		the_excerpt();
	}

}


// Creates a formatted excerpt of the $text (html text) variable. $excerpt_length is in words
function tpl_trim_excerpt( $text, $excerpt_length = 55 ) {

	// Other than $allowed_tags will be removed from the $text
	$allowed_tags = array( 'b' ,'strong', 'i', 'em', 'br', 'p', 'blockquote', 'span', 'ul', 'li', 'ol', 'dt', 'dl', 'dd', 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
    $text = apply_filters( 'the_content', $text );
    $text = str_replace( '\]\]\>', ']]&gt;', $text );

	$allowed_tags_text = '';
	foreach ( $allowed_tags as $tag ) {
		$allowed_tags_text .= '<' . $tag . '>,';
	}
	$allowed_tags_text = rtrim( $allowed_tags_text, ',' );

    $text = strip_tags( $text, $allowed_tags_text );
    $words = explode( ' ', $text, $excerpt_length + 1 );

    if ( count( $words ) > $excerpt_length ) {
        array_pop( $words );
		$open_tags = array();

		foreach ( $words as $i => $word ) {
			$word = $word . ' ';
			foreach ( $allowed_tags as $tag ) {
				if ( strpos( $word, '<' . $tag . ' ' ) !== false || strpos( $word, '<' . $tag . '>' ) ) {
					if ( $tag != 'br' ) {
						$open_tags[] = $tag;
					}
				}
				if ( strpos( $word, '</' . $tag . ' ' ) !== false || strpos( $word, '</' . $tag . '>' ) ) {
					for ( $j = count( $open_tags ) - 1; $j >= 0; $j-- ) {
					    if ( $open_tags[$j] == $tag ) {
							array_splice( $open_tags, $j, 1 );
						}
					}
				}
			}
		}

		$words[] = '...';
		$words[] = tpl_excerpt_more( '' );
		if ( !empty( $open_tags ) ) {
			foreach ( array_reverse( $open_tags ) as $tag ) {
				$words[] = '</' . $tag . '>';
			}
		}
		$text = implode( ' ', $words );
    }

    return wpautop( $text );

}
