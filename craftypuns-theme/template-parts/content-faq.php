<?php
/**
 * FAQ block: 3 Q&As from postmeta (_faq_q1/_faq_a1 ... _faq_q3/_faq_a3).
 * Emits FAQPage JSON-LD via craftypuns_faq_schema().
 */
$faqs = craftypuns_get_faqs( get_the_ID() );
if ( empty( $faqs ) ) {
	return;
}
?>
<section class="mt-10 border-t border-slate-100 pt-10">
	<h2 class="text-2xl font-bold"><?php esc_html_e( 'FAQ', 'craftypuns' ); ?></h2>
	<div class="mt-6 space-y-6">
		<?php foreach ( $faqs as $faq ) : ?>
			<div>
				<h3 class="font-semibold"><?php echo esc_html( $faq['q'] ); ?></h3>
				<p class="mt-1 text-slate-600"><?php echo esc_html( $faq['a'] ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php craftypuns_faq_schema( get_the_ID() ); ?>
