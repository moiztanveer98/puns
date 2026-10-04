<?php
/**
 * Hub / Sub-hub landing page: term description + paginated post list + breadcrumb.
 */
get_header();
$term = get_queried_object();
?>

<main class="mx-auto max-w-6xl px-4 py-10">
	<?php craftypuns_breadcrumbs(); ?>

	<h1 class="mt-4 text-3xl font-extrabold tracking-tight"><?php single_cat_title(); ?></h1>
	<?php if ( $term->description ) : ?>
		<p class="mt-3 max-w-3xl text-lg text-slate-600"><?php echo esc_html( $term->description ); ?></p>
	<?php endif; ?>

	<?php
	$children = get_categories( array( 'parent' => $term->term_id, 'hide_empty' => false ) );
	if ( $children ) :
		?>
		<div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $children as $child ) : ?>
				<a class="rounded-2xl border border-slate-100 p-5 transition hover:border-amber-300 hover:shadow-md" href="<?php echo esc_url( get_term_link( $child ) ); ?>">
					<h2 class="font-semibold"><?php echo esc_html( $child->name ); ?></h2>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<a class="block rounded-2xl border border-slate-100 p-5 transition hover:border-amber-300 hover:shadow-md" href="<?php the_permalink(); ?>">
					<h3 class="font-semibold"><?php the_title(); ?></h3>
					<p class="mt-2 text-sm text-slate-600"><?php echo esc_html( get_the_excerpt() ); ?></p>
				</a>
				<?php
			endwhile;
			?>
		</div>
		<div class="mt-10">
			<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
		</div>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
