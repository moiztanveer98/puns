<?php
/**
 * Topic / use-hub / outer-section post template.
 * Order: H1, declaration-first intro, H2 pun sections, generator box,
 * divider, FAQ (+ JSON-LD), Related Puns (bridge links).
 */
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main class="mx-auto max-w-3xl px-4 py-10">
		<?php craftypuns_breadcrumbs(); ?>

		<article>
			<h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl"><?php the_title(); ?></h1>

			<?php get_template_part( 'template-parts/content-pun-sections' ); ?>

			<div class="mt-10 rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center">
				<p class="font-semibold"><?php printf( esc_html__( 'Make Your Own %s Pun', 'craftypuns' ), esc_html( get_the_title() ) ); ?></p>
				<div class="mt-4">
					<?php echo do_shortcode( '[pun_generator topic="' . esc_attr( get_the_title() ) . '"]' ); ?>
				</div>
			</div>

			<?php get_template_part( 'template-parts/content-faq' ); ?>
			<?php get_template_part( 'template-parts/content-related-puns' ); ?>
		</article>
	</main>
	<?php
endwhile;

get_footer();
