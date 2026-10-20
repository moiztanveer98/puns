<?php
/**
 * Homepage: hero + Pun Generator, how-it-works, most popular puns, topic hub grid,
 * puns-by-occasion grid, "Coming Up" seasonal module, why-us, learn-about-puns links.
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
			<h1 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-5xl"><?php esc_html_e( 'Original Puns for Every Topic, Occasion and Mood', 'craftypuns' ); ?></h1>
			<p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600"><?php esc_html_e( 'Browse hundreds of pun pages by topic, or make your own in seconds with our free Pun Generator.', 'craftypuns' ); ?></p>
			<p class="mx-auto mt-4 max-w-2xl text-slate-600"><?php esc_html_e( 'Crafty Puns is a free, original wordplay library covering animals, food, holidays, relationships, and hundreds of everyday topics. Every pun, one-liner and joke on this site is written from scratch, organized by category, and ready to copy straight into a caption, card or conversation.', 'craftypuns' ); ?></p>
			<div class="mt-8">
				<?php echo do_shortcode( '[pun_generator]' ); ?>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-6xl px-4 py-16">
		<h2 class="text-center text-2xl font-bold"><?php esc_html_e( 'How It Works', 'craftypuns' ); ?></h2>
		<div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
			<div class="text-center">
				<div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg font-bold text-amber-700">1</div>
				<h3 class="mt-4 font-semibold"><?php esc_html_e( 'Pick a Topic', 'craftypuns' ); ?></h3>
				<p class="mt-2 text-sm text-slate-600"><?php esc_html_e( 'Choose from animals, food, holidays and more, or search for exactly what you need.', 'craftypuns' ); ?></p>
			</div>
			<div class="text-center">
				<div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg font-bold text-amber-700">2</div>
				<h3 class="mt-4 font-semibold"><?php esc_html_e( 'Browse or Generate', 'craftypuns' ); ?></h3>
				<p class="mt-2 text-sm text-slate-600"><?php esc_html_e( 'Read curated pun lists by category, or use the free Pun Generator for an instant random mix.', 'craftypuns' ); ?></p>
			</div>
			<div class="text-center">
				<div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg font-bold text-amber-700">3</div>
				<h3 class="mt-4 font-semibold"><?php esc_html_e( 'Copy & Share', 'craftypuns' ); ?></h3>
				<p class="mt-2 text-sm text-slate-600"><?php esc_html_e( 'Tap any pun to copy it instantly, then paste it into a caption, card or chat.', 'craftypuns' ); ?></p>
			</div>
		</div>
	</section>

	<section class="bg-slate-50 py-16">
		<div class="mx-auto max-w-6xl px-4">
			<h2 class="text-2xl font-bold"><?php esc_html_e( 'Most Popular Puns', 'craftypuns' ); ?></h2>
			<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
				<?php
				$popular_slugs = array( 'dog-puns', 'cat-puns', 'christmas-puns', 'birthday-puns', 'halloween-puns', 'pizza-puns', 'dad-joke-puns', 'love-puns' );
				$popular_posts = get_posts( array(
					'post_type'           => 'post',
					'post_name__in'       => $popular_slugs,
					'posts_per_page'      => count( $popular_slugs ),
					'orderby'             => 'post_name__in',
					'ignore_sticky_posts' => true,
				) );
				foreach ( $popular_posts as $pop_post ) :
					?>
					<a class="block overflow-hidden rounded-2xl border border-slate-100 bg-white transition hover:border-amber-300 hover:shadow-md" href="<?php echo esc_url( get_permalink( $pop_post ) ); ?>">
						<?php if ( has_post_thumbnail( $pop_post ) ) : ?>
							<?php echo get_the_post_thumbnail( $pop_post, 'medium', array( 'class' => 'mx-auto aspect-[3/2] w-full object-cover object-center' ) ); ?>
						<?php endif; ?>
						<div class="p-5">
							<h3 class="font-semibold leading-snug"><?php echo esc_html( get_the_title( $pop_post ) ); ?></h3>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-6xl px-4 py-16">
		<h2 class="text-2xl font-bold"><?php esc_html_e( 'Browse Puns by Topic', 'craftypuns' ); ?></h2>
		<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			$hubs = get_categories( array( 'parent' => 0, 'hide_empty' => true ) );
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
			<h2 class="text-2xl font-bold"><?php esc_html_e( 'Puns by Occasion', 'craftypuns' ); ?></h2>
			<div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
				<?php
				$occasions = array(
					'birthday-puns'         => __( 'Birthday', 'craftypuns' ),
					'christmas-puns'        => __( 'Christmas', 'craftypuns' ),
					'halloween-puns'        => __( 'Halloween', 'craftypuns' ),
					'valentines-day-puns'   => __( "Valentine's Day", 'craftypuns' ),
					'thanksgiving-puns'     => __( 'Thanksgiving', 'craftypuns' ),
					'new-year-puns'         => __( 'New Year', 'craftypuns' ),
					'easter-puns'           => __( 'Easter', 'craftypuns' ),
					'st-patricks-day-puns'  => __( "St. Patrick's Day", 'craftypuns' ),
				);
				foreach ( $occasions as $slug => $label ) :
					?>
					<a class="rounded-full border border-slate-200 bg-white px-4 py-3 text-center text-sm font-medium text-slate-700 transition hover:border-amber-300 hover:text-amber-700" href="<?php echo esc_url( home_url( '/' . $slug . '/' ) ); ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-6xl px-4 py-16">
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
	</section>

	<section class="bg-slate-50 py-16">
		<div class="mx-auto max-w-6xl px-4">
			<h2 class="text-2xl font-bold"><?php esc_html_e( 'Why Crafty Puns', 'craftypuns' ); ?></h2>
			<div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
				<div>
					<h3 class="font-semibold"><?php esc_html_e( '100% Original', 'craftypuns' ); ?></h3>
					<p class="mt-2 text-sm text-slate-600"><?php esc_html_e( 'Every pun is written for this site, not scraped or recycled from old joke books.', 'craftypuns' ); ?></p>
				</div>
				<div>
					<h3 class="font-semibold"><?php esc_html_e( 'Always Free', 'craftypuns' ); ?></h3>
					<p class="mt-2 text-sm text-slate-600"><?php esc_html_e( 'No sign-up, no paywall. Browse, generate and copy as many puns as you want.', 'craftypuns' ); ?></p>
				</div>
				<div>
					<h3 class="font-semibold"><?php esc_html_e( 'Organized by Topic', 'craftypuns' ); ?></h3>
					<p class="mt-2 text-sm text-slate-600"><?php esc_html_e( 'Hundreds of category pages make it fast to find puns for exactly what you need.', 'craftypuns' ); ?></p>
				</div>
				<div>
					<h3 class="font-semibold"><?php esc_html_e( 'One-Tap Copy', 'craftypuns' ); ?></h3>
					<p class="mt-2 text-sm text-slate-600"><?php esc_html_e( 'Every pun line has a copy button, so you can paste it anywhere in one tap.', 'craftypuns' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-6xl px-4 py-16">
		<h2 class="text-2xl font-bold"><?php esc_html_e( 'Learn About Puns', 'craftypuns' ); ?></h2>
		<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<a class="rounded-2xl border border-slate-100 p-5 transition hover:border-amber-300 hover:shadow-md" href="<?php echo esc_url( home_url( '/what-is-a-pun/' ) ); ?>">
				<h3 class="font-semibold"><?php esc_html_e( 'What Is a Pun?', 'craftypuns' ); ?></h3>
			</a>
			<a class="rounded-2xl border border-slate-100 p-5 transition hover:border-amber-300 hover:shadow-md" href="<?php echo esc_url( home_url( '/types-of-puns/' ) ); ?>">
				<h3 class="font-semibold"><?php esc_html_e( 'Types of Puns', 'craftypuns' ); ?></h3>
			</a>
			<a class="rounded-2xl border border-slate-100 p-5 transition hover:border-amber-300 hover:shadow-md" href="<?php echo esc_url( home_url( '/how-to-write-a-pun/' ) ); ?>">
				<h3 class="font-semibold"><?php esc_html_e( 'How to Write a Pun', 'craftypuns' ); ?></h3>
			</a>
			<a class="rounded-2xl border border-slate-100 p-5 transition hover:border-amber-300 hover:shadow-md" href="<?php echo esc_url( home_url( '/best-puns/' ) ); ?>">
				<h3 class="font-semibold"><?php esc_html_e( 'The 100 Best Puns', 'craftypuns' ); ?></h3>
			</a>
		</div>
	</section>
</main>

<?php get_footer(); ?>
