<?php
/**
 * 404 Not Found: on-brand error page with search and quick links back into the site.
 */
get_header();
?>
<main class="mx-auto max-w-3xl px-4 py-16 text-center">
	<p class="text-sm font-semibold uppercase tracking-wide text-amber-600"><?php esc_html_e( '404 Error', 'craftypuns' ); ?></p>
	<h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl"><?php esc_html_e( "That Pun Doesn't Exist... Yet", 'craftypuns' ); ?></h1>
	<p class="mx-auto mt-4 max-w-xl text-lg text-slate-600"><?php esc_html_e( "The page you're looking for has moved, been renamed, or never existed. Try searching, or jump back to a popular topic below.", 'craftypuns' ); ?></p>

	<form class="mx-auto mt-8 flex max-w-md items-center gap-2" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="sr-only" for="cp-404-search"><?php esc_html_e( 'Search', 'craftypuns' ); ?></label>
		<input id="cp-404-search" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900" type="search" name="s" placeholder="<?php esc_attr_e( 'Search puns…', 'craftypuns' ); ?>" />
		<button type="submit" class="rounded-full bg-amber-400 px-5 py-2 font-semibold text-slate-900 transition hover:bg-amber-300"><?php esc_html_e( 'Search', 'craftypuns' ); ?></button>
	</form>

	<div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
		<?php
		$hubs = get_categories( array( 'parent' => 0, 'hide_empty' => true ) );
		foreach ( $hubs as $hub ) :
			?>
			<a class="rounded-2xl border border-slate-100 p-5 text-left transition hover:border-amber-300 hover:shadow-md" href="<?php echo esc_url( get_term_link( $hub ) ); ?>">
				<h2 class="font-semibold"><?php echo esc_html( $hub->name ); ?></h2>
			</a>
		<?php endforeach; ?>
	</div>

	<a class="mt-10 inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-3 font-semibold text-white transition hover:bg-slate-700" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php esc_html_e( 'Back to Homepage', 'craftypuns' ); ?>
	</a>
</main>
<?php
get_footer();
