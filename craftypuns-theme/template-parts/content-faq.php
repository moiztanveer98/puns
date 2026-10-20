<?php
/**
 * The FAQ itself is already rendered visually inside the_content() (see
 * content-pun-sections.php) as a "## FAQ" markdown section. This part only
 * emits the matching FAQPage JSON-LD, parsed from that same content (or from
 * legacy _faq_q1/_faq_a1 postmeta when present) so it isn't shown twice.
 */
craftypuns_faq_schema( get_the_ID() );
