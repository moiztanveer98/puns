<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-white text-slate-900' ); ?>>
<?php wp_body_open(); ?>

<header class="border-b border-slate-100">
	<div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
		<a class="flex items-center" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo '<span class="text-xl font-bold">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
			}
			?>
		</a>
		<nav class="hidden lg:block" aria-label="<?php esc_attr_e( 'Primary', 'craftypuns' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '<ul class="flex items-center gap-6">%3$s</ul>',
				'fallback_cb'    => 'craftypuns_fallback_primary_menu',
			) );
			?>
		</nav>
		<button class="lg:hidden" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'craftypuns' ); ?>" onclick="document.getElementById('cp-mobile-nav').classList.toggle('hidden')">
			<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
		</button>
	</div>
	<div id="cp-mobile-nav" class="hidden border-t border-slate-100 px-4 py-4 lg:hidden">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '<ul class="flex flex-col gap-3">%3$s</ul>',
			'fallback_cb'    => 'craftypuns_fallback_primary_menu',
		) );
		?>
	</div>
</header>
