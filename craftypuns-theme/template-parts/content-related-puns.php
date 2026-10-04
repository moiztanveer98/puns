<?php
/**
 * Related Puns: reads _bridge_links postmeta ([{label, slug}, ...] set by the WXR import)
 * and renders descriptive-anchor links — never "click here".
 */
$links = craftypuns_get_bridge_links( get_the_ID() );
if ( empty( $links ) ) {
	return;
}
?>
<section class="mt-10 border-t border-slate-100 pt-10">
	<h2 class="text-2xl font-bold"><?php esc_html_e( 'Related Puns', 'craftypuns' ); ?></h2>
	<ul class="mt-4 flex flex-wrap gap-3">
		<?php foreach ( $links as $link ) : ?>
			<li>
				<a class="inline-block rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-amber-300 hover:text-amber-700" href="<?php echo esc_url( home_url( '/' . trim( $link['slug'], '/' ) . '/' ) ); ?>">
					<?php echo esc_html( $link['label'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
