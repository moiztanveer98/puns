<?php
/**
 * Homepage: hero + Pun Generator, top-level hub grid, "Coming Up" seasonal module.
 *
 * The seasonal links below are hardcoded from data/seasonal.json at generation time
 * (nearest 3 upcoming events as of the Oct 2026 build) — no runtime JSON dependency.
 * Update this block directly as events pass; see SETUP.md.
 */
get_header();
?>

<main>
	<section class="bg-gradient-to-b from-amber-50 to-white py-16">
		<div class="mx-auto max-w-4xl px-4 text-center">
			<?php if ( has_custom_logo() ) : the_custom_logo(); endif; ?>
			<h1 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-5xl"><?php esc_html_e( 'Original Puns for Every Topic, Occasion and Mood', 'craftypuns' ); ?></h1>
			<p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600"><?php esc_html_e( 'Browse hundreds of pun pages by topic, or make your own in seconds with our free Pun Generator.', 'craftypuns' ); ?></p>
			<div class="mt-8">
				<?php echo do_shortcode( '[pun_generator]' ); ?>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-6xl px-4 py-16">
		<h2 class="text-2xl font-bold"><?php esc_html_e( 'Browse Puns by Topic', 'craftypuns' ); ?></h2>
		<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			$hubs = get_categories( array( 'parent' => 0, 'hide_empty' => false ) );
			foreach ( $hubs as $hub ) :
				?>
				<a class="rounded-2xl border border-slate-100 p-6 transition hover:border-amber-300 hover:shadow-md" href="<?php echo esc_url( get_term_link( $hub ) ); ?>">
					<h3 class="text-lg font-semibold"><?php echo esc_html( $hub->name ); ?></h3>
					<?php if ( $hub->description ) : ?>
						<p class="mt-2 text-sm text-slate-600"><?php echo esc_html( wp_trim_words( $hub->description, 18 ) ); ?></p>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="bg-slate-50 py-16">
		<div class="mx-auto max-w-6xl px-4">
			<h2 class="text-2xl font-bold"><?php esc_html_e( 'Coming Up', 'craftypuns' ); ?></h2>
			<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
				<a class="rounded-2xl bg-white p-6 shadow-sm transition hover:shadow-md" href="<?php echo esc_url( home_url( '/halloween-puns/' ) ); ?>">
					<p class="text-xs font-semibold uppercase tracking-wide text-amber-600"><?php esc_html_e( 'Oct 31', 'craftypuns' ); ?></p>
					<h3 class="mt-1 text-lg font-semibold"><?php esc_html_e( 'Halloween Puns', 'craftypuns' ); ?></h3>
				</a>
				<a class="rounded-2xl bg-white p-6 shadow-sm transition hover:shadow-md" href="<?php echo esc_url( home_url( '/thanksgiving-puns/' ) ); ?>">
					<p class="text-xs font-semibold uppercase tracking-wide text-amber-600"><?php esc_html_e( 'Nov 26', 'craftypuns' ); ?></p>
					<h3 class="mt-1 text-lg font-semibold"><?php esc_html_e( 'Thanksgiving Puns', 'craftypuns' ); ?></h3>
				</a>
				<a class="rounded-2xl bg-white p-6 shadow-sm transition hover:shadow-md" href="<?php echo esc_url( home_url( '/christmas-puns/' ) ); ?>">
					<p class="text-xs font-semibold uppercase tracking-wide text-amber-600"><?php esc_html_e( 'Dec 25', 'craftypuns' ); ?></p>
					<h3 class="mt-1 text-lg font-semibold"><?php esc_html_e( 'Christmas Puns', 'craftypuns' ); ?></h3>
				</a>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
