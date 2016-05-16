<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">

		<?php wp_head(); ?>

	</head>

	<?php
	$layout = tpl_get_layout();
	$body_class = 'layout-sidebar-' . $layout;
	?>
    <body <?php body_class( $body_class ); ?>>
		<div id="wrapper" class="wrapper">

			<header>
				<div class="row headertop">
					<div class="logo-wrapper column-6">
						<?php tpl_logo(); ?>
					</div>
					<div class="icon-line column-6">
						<?php tpl_value( 'social_icons' ); ?>
					</div>
				</div>
				<div class="row column-12 menu-wrapper">
				    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'menu_id' => 'primary_menu',
                        'container' => 'nav',
                        'items_wrap' => '<ul id="%1$s" class="%2$s desktop-only">%3$s</ul>',
                    ) );
                    tpl_hamburger_icon( 'primary_menu', 'mobile-only' );
					?>
				</div>
			</header>
