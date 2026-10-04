	<footer class="bg-slate-900 py-12 text-slate-300">
		<div class="mx-auto max-w-6xl px-4">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'walker'         => null,
				'fallback_cb'    => 'craftypuns_fallback_footer_menu',
			) );
			?>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
