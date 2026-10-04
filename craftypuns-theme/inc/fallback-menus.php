<?php
/**
 * Fallback nav menus — plain HTML mirroring the Header & Footer sheet exactly,
 * so navigation works correctly before menus are assigned in WP Admin.
 * Generated from data/header-footer.json at theme-build time (not read at runtime).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function craftypuns_fallback_primary_menu() {
	?>
	<ul class="flex items-center gap-6">
		<li class="group relative">
			<button class="flex items-center gap-1 font-semibold text-slate-800 hover:text-amber-600" aria-haspopup="true">
				<?php esc_html_e( 'Puns', 'craftypuns' ); ?>
				<svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
			</button>
			<div class="invisible absolute left-1/2 top-full z-40 w-[640px] -translate-x-1/2 translate-y-2 rounded-2xl border border-slate-100 bg-white p-6 opacity-0 shadow-xl transition group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
				<div class="grid grid-cols-3 gap-6">
					<div>
						<p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400"><?php esc_html_e( 'By Topic', 'craftypuns' ); ?></p>
						<ul class="space-y-2 text-sm">
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/animal-puns/' ) ); ?>"><?php esc_html_e( 'Animal Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/food-and-drink-puns/' ) ); ?>"><?php esc_html_e( 'Food & Drink Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/nature-and-science-puns/' ) ); ?>"><?php esc_html_e( 'Nature & Science Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/holiday-and-event-puns/' ) ); ?>"><?php esc_html_e( 'Holiday & Event Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/people-and-relationship-puns/' ) ); ?>"><?php esc_html_e( 'People & Relationship Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/object-and-everyday-puns/' ) ); ?>"><?php esc_html_e( 'Object & Everyday Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/sport-and-hobby-puns/' ) ); ?>"><?php esc_html_e( 'Sport & Hobby Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/work-and-school-puns/' ) ); ?>"><?php esc_html_e( 'Work & School Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/fantasy-and-pop-culture-puns/' ) ); ?>"><?php esc_html_e( 'Fantasy & Pop Culture Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/word-and-name-puns/' ) ); ?>"><?php esc_html_e( 'Word & Name Puns', 'craftypuns' ); ?></a></li>
						</ul>
					</div>
					<div>
						<p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400"><?php esc_html_e( 'By Use', 'craftypuns' ); ?></p>
						<ul class="space-y-2 text-sm">
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/puns-for-instagram-captions/' ) ); ?>"><?php esc_html_e( 'Puns for Instagram Captions', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/puns-for-kids/' ) ); ?>"><?php esc_html_e( 'Puns for Kids', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/love-puns/' ) ); ?>"><?php esc_html_e( 'Love Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/dad-joke-puns/' ) ); ?>"><?php esc_html_e( 'Dad Joke Puns', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/best-puns/' ) ); ?>"><?php esc_html_e( 'Best Puns of All Time', 'craftypuns' ); ?></a></li>
						</ul>
					</div>
					<div>
						<p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400"><?php esc_html_e( 'Learn', 'craftypuns' ); ?></p>
						<ul class="space-y-2 text-sm">
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/what-is-a-pun/' ) ); ?>"><?php esc_html_e( 'What Is a Pun?', 'craftypuns' ); ?></a></li>
							<li><a class="text-slate-700 hover:text-amber-600" href="<?php echo esc_url( home_url( '/types-of-puns/' ) ); ?>"><?php esc_html_e( 'Types of Puns', 'craftypuns' ); ?></a></li>
						</ul>
					</div>
				</div>
			</div>
		</li>
		<li><a class="font-semibold text-slate-800 hover:text-amber-600" href="<?php echo esc_url( home_url( '/riddles/' ) ); ?>"><?php esc_html_e( 'Riddles', 'craftypuns' ); ?></a></li>
		<li><a class="font-semibold text-slate-800 hover:text-amber-600" href="<?php echo esc_url( home_url( '/meanings/' ) ); ?>"><?php esc_html_e( 'Meanings', 'craftypuns' ); ?></a></li>
		<li>
			<a class="inline-flex items-center rounded-full bg-amber-400 px-5 py-2 font-semibold text-slate-900 transition hover:bg-amber-300" href="<?php echo esc_url( home_url( '/pun-generator/' ) ); ?>">
				<?php esc_html_e( 'Pun Generator', 'craftypuns' ); ?>
			</a>
		</li>
	</ul>
	<?php
}

function craftypuns_fallback_footer_menu() {
	?>
	<div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
		<div>
			<a class="inline-block" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
					<span class="text-xl font-bold text-white"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
			</a>
			<p class="mt-3 text-sm text-slate-400"><?php esc_html_e( 'Crafty Puns is a wordplay library of original puns, riddles, and a free Pun Generator.', 'craftypuns' ); ?></p>
			<div class="mt-4 flex gap-3">
				<a class="text-slate-400 hover:text-amber-400" href="https://www.pinterest.com/craftypuns/" rel="me noopener" aria-label="Pinterest">Pinterest</a>
			</div>
		</div>
		<div>
			<p class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-300"><?php esc_html_e( 'Popular Hubs', 'craftypuns' ); ?></p>
			<ul class="space-y-2 text-sm text-slate-400">
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/animal-puns/' ) ); ?>"><?php esc_html_e( 'Animal Puns', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/food-and-drink-puns/' ) ); ?>"><?php esc_html_e( 'Food & Drink Puns', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/holiday-and-event-puns/' ) ); ?>"><?php esc_html_e( 'Holiday & Event Puns', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/people-and-relationship-puns/' ) ); ?>"><?php esc_html_e( 'People & Relationship Puns', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/puns-for-instagram-captions/' ) ); ?>"><?php esc_html_e( 'Puns for Instagram Captions', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/puns-for-kids/' ) ); ?>"><?php esc_html_e( 'Puns for Kids', 'craftypuns' ); ?></a></li>
			</ul>
		</div>
		<div>
			<p class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-300"><?php esc_html_e( 'Explore', 'craftypuns' ); ?></p>
			<ul class="space-y-2 text-sm text-slate-400">
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/pun-generator/' ) ); ?>"><?php esc_html_e( 'Pun Generator', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/riddles/' ) ); ?>"><?php esc_html_e( 'Riddles', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/meanings/' ) ); ?>"><?php esc_html_e( 'Meanings', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/what-is-a-pun/' ) ); ?>"><?php esc_html_e( 'What Is a Pun?', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/types-of-puns/' ) ); ?>"><?php esc_html_e( 'Types of Puns', 'craftypuns' ); ?></a></li>
			</ul>
		</div>
		<div>
			<p class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-300"><?php esc_html_e( 'Company', 'craftypuns' ); ?></p>
			<ul class="space-y-2 text-sm text-slate-400">
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/our-authors/' ) ); ?>"><?php esc_html_e( 'Our Authors', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/editorial-guidelines/' ) ); ?>"><?php esc_html_e( 'Editorial Guidelines', 'craftypuns' ); ?></a></li>
				<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'craftypuns' ); ?></a></li>
			</ul>
		</div>
	</div>
	<div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-slate-800 pt-6 text-sm text-slate-500 sm:flex-row">
		<p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php esc_html_e( 'Crafty Puns', 'craftypuns' ); ?></p>
		<ul class="flex flex-wrap gap-4">
			<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'craftypuns' ); ?></a></li>
			<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'craftypuns' ); ?></a></li>
			<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>"><?php esc_html_e( 'Disclaimer', 'craftypuns' ); ?></a></li>
			<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/cookie-policy/' ) ); ?>"><?php esc_html_e( 'Cookie Policy', 'craftypuns' ); ?></a></li>
			<li><a class="hover:text-amber-400" href="<?php echo esc_url( home_url( '/dmca/' ) ); ?>"><?php esc_html_e( 'DMCA', 'craftypuns' ); ?></a></li>
		</ul>
	</div>
	<?php
}
