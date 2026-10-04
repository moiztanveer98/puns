<?php
/**
 * Fallback template for any request type not covered by a more specific template.
 */
get_header();
?>

<main class="mx-auto max-w-6xl px-4 py-10">
	<?php craftypuns_breadcrumbs(); ?>

	<?php if ( have_posts() ) : ?>
		<div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<a class="block rounded-2xl border border-slate-100 p-5 transition hover:border-amber-300 hover:shadow-md" href="<?php the_permalink(); ?>">
					<h2 class="font-semibold"><?php the_title(); ?></h2>
					<p class="mt-2 text-sm text-slate-600"><?php echo esc_html( get_the_excerpt() ); ?></p>
				</a>
				<?php
			endwhile;
			?>
		</div>
		<div class="mt-10">
			<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'craftypuns' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
