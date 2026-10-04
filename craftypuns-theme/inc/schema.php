<?php
/**
 * Sitewide JSON-LD: Organization + WebSite, BreadcrumbList per page,
 * FAQPage per post with an FAQ block. Always emitted regardless of SEO plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function craftypuns_organization_schema() {
	$logo = has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : '';

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type' => 'Organization',
				'@id'   => home_url( '/#organization' ),
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
				'logo'  => $logo,
				'sameAs' => array_filter( array(
					'https://www.pinterest.com/craftypuns/',
				) ),
			),
			array(
				'@type'           => 'WebSite',
				'@id'             => home_url( '/#website' ),
				'name'            => get_bloginfo( 'name' ),
				'url'             => home_url( '/' ),
				'publisher'       => array( '@id' => home_url( '/#organization' ) ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => home_url( '/?s={search_term_string}' ),
					'query-input' => 'required name=search_term_string',
				),
			),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'craftypuns_organization_schema', 2 );

function craftypuns_breadcrumb_schema() {
	$items = array();
	$position = 1;

	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $position++,
		'name'     => __( 'Home', 'craftypuns' ),
		'item'     => home_url( '/' ),
	);

	if ( is_singular( 'post' ) ) {
		$cats = get_the_category();
		if ( $cats ) {
			$cat = $cats[0];
			if ( $cat->parent ) {
				$parent = get_category( $cat->parent );
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $position++,
					'name'     => $parent->name,
					'item'     => get_term_link( $parent ),
				);
			}
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $cat->name,
				'item'     => get_term_link( $cat ),
			);
		}
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	} elseif ( is_category() ) {
		$term = get_queried_object();
		if ( $term->parent ) {
			$parent = get_category( $term->parent );
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $parent->name,
				'item'     => get_term_link( $parent ),
			);
		}
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => $term->name,
			'item'     => get_term_link( $term ),
		);
	} elseif ( is_page() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	} else {
		return;
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'craftypuns_breadcrumb_schema', 3 );

/**
 * FAQPage schema for a post, built from the 3 FAQ postmeta pairs
 * (_faq_q1/_faq_a1, _faq_q2/_faq_a2, _faq_q3/_faq_a3) set by the WXR import.
 */
function craftypuns_faq_schema( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$faqs    = craftypuns_get_faqs( $post_id );

	if ( empty( $faqs ) ) {
		return;
	}

	$main_entity = array();
	foreach ( $faqs as $faq ) {
		$main_entity[] = array(
			'@type'          => 'Question',
			'name'           => $faq['q'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $faq['a'],
			),
		);
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $main_entity,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}

/**
 * Reads the 3 Q&A postmeta pairs, skipping any that are empty.
 */
function craftypuns_get_faqs( $post_id ) {
	$faqs = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		$q = get_post_meta( $post_id, "_faq_q{$i}", true );
		$a = get_post_meta( $post_id, "_faq_a{$i}", true );
		if ( $q && $a ) {
			$faqs[] = array( 'q' => $q, 'a' => $a );
		}
	}
	return $faqs;
}
