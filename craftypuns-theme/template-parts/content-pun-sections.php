<?php
/**
 * Renders the H2 pun sections from post_content as-is. The WXR import writes
 * real <h2>…</h2> markup in H2 Template sheet order, so this part just
 * outputs the_content() inside a prose-styled wrapper.
 */
?>
<div class="prose prose-slate mt-8 max-w-none prose-h2:text-2xl prose-h2:font-bold prose-h2:mt-10">
	<?php the_content(); ?>
</div>
