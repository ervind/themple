<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">

		<?php wp_head(); ?>

	</head>

	<?php
	$layout = tpl_get_layout();
	$body_class = 'tpl-layout-sidebar-' . $layout;
	?>
    <body <?php body_class( $body_class ); ?>>
		<div id="wrapper" class="tpl-wrapper">

			<header>
				<div class="tpl-grid-row tpl-top-bar">
					<div class="tpl-icon-line tpl-grid-column-12">
						<?php tpl_value( 'social_icons' ); ?>
					</div>
				</div>
				<div class="tpl-grid-row tpl-headertop">
					<div class="tpl-logo-wrapper tpl-grid-column-12">
						<?php tpl_logo(); ?>
					</div>
				</div>
				<div class="tpl-grid-row">
					<div class="tpl-grid-column-12 tpl-menu-wrapper">
					    <?php
	                    wp_nav_menu( array(
	                        'theme_location' => 'primary',
	                        'menu_id' => 'primary_menu',
	                        'container' => 'nav',
	                        'items_wrap' => '<ul id="%1$s" class="%2$s tpl-desktop-only">%3$s</ul>',
	                    ) );
	                    tpl_hamburger_icon( 'primary_menu', 'tpl-mobile-only' );
						?>
					</div>
				</div>
			</header>
