<?php
/**
 * Static page template (About Us, Contact, Privacy Policy, etc.).
 */
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main class="mx-auto max-w-3xl px-4 py-10">
		<?php craftypuns_breadcrumbs(); ?>
		<article>
			<h1 class="mt-4 text-3xl font-extrabold tracking-tight"><?php the_title(); ?></h1>
			<div class="prose prose-slate mt-6 max-w-none">
				<?php the_content(); ?>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
