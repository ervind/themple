<?php

// Functions for widgets and widget areas



// Register sidebars
function tpl_widgets_init() {

	$args = array(
		'name'          => __( 'Right sidebar', 'themple-starter' ),
		'id'            => 'right-sidebar',
		'description'   => __( 'Sidebar displayed in the right column', 'themple-starter' ),
		'class'         => 'right-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget b'. tpl_get_value( 'starter_topmargin' ) .' %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>'
	);
	register_sidebar( $args );

	$args = array(
		'name'          => __( 'Footer sidebar #1', 'themple-starter' ),
		'id'            => 'footer-sidebar-1',
		'class'         => 'footer-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>'
	);
	register_sidebar( $args );

	$args = array(
		'name'          => __( 'Footer sidebar #2', 'themple-starter' ),
		'id'            => 'footer-sidebar-2',
		'class'         => 'footer-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>'
	);
	register_sidebar( $args );

	$args = array(
		'name'          => __( 'Footer sidebar #3', 'themple-starter' ),
		'id'            => 'footer-sidebar-3',
		'class'         => 'footer-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>'
	);
	register_sidebar( $args );

	$args = array(
		'name'          => __( 'Footer sidebar #4', 'themple-starter' ),
		'id'            => 'footer-sidebar-4',
		'class'         => 'footer-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>'
	);
	register_sidebar( $args );

}
add_action( 'widgets_init', 'tpl_widgets_init' );
