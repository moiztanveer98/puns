<?php
/**
 * Sitewide JSON-LD: Organization + WebSite, BreadcrumbList per page,
 * FAQPage per post with an FAQ block. Always emitted regardless of SEO plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function craftypuns_organization_schema() {
	$logo_id  = has_custom_logo() ? get_theme_mod( 'custom_logo' ) : 0;
	$logo_src = $logo_id ? wp_get_attachment_image_src( $logo_id, 'full' ) : false;
	$logo     = $logo_src ? array(
		'@type'  => 'ImageObject',
		'url'    => $logo_src[0],
		'width'  => $logo_src[1],
		'height' => $logo_src[2],
	) : '';

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
 * Article schema for every pun post, with an embedded ImageObject for the
 * featured image when one is set.
 */
function craftypuns_article_schema() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post_id = get_the_ID();
	$schema  = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'@id'              => get_permalink( $post_id ) . '#article',
		'headline'         => get_the_title( $post_id ),
		'description'      => craftypuns_get_seo_description(),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ),
		),
		'author'           => array( '@id' => home_url( '/#organization' ) ),
		'publisher'        => array( '@id' => home_url( '/#organization' ) ),
		'datePublished'    => get_the_date( 'c', $post_id ),
		'dateModified'     => get_the_modified_date( 'c', $post_id ),
	);

	if ( has_post_thumbnail( $post_id ) ) {
		$thumb_id = get_post_thumbnail_id( $post_id );
		$src      = wp_get_attachment_image_src( $thumb_id, 'large' );
		if ( $src ) {
			$schema['image'] = array(
				'@type'  => 'ImageObject',
				'url'    => $src[0],
				'width'  => $src[1],
				'height' => $src[2],
			);
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'craftypuns_article_schema', 4 );

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
 * Reads the 3 Q&A postmeta pairs (legacy WXR import path), skipping empties.
 * Falls back to parsing the FAQ section directly out of post_content, since
 * articles published via the REST API never had this postmeta set, but do
 * have a "## FAQ" section rendered as <h2>FAQ</h2><p><strong>Q</strong><br>A</p>.
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
	if ( ! empty( $faqs ) ) {
		return $faqs;
	}
	return craftypuns_get_faqs_from_content( $post_id );
}

function craftypuns_get_faqs_from_content( $post_id ) {
	$content = get_post_field( 'post_content', $post_id );
	if ( ! $content ) {
		return array();
	}
	// Raw post_content has no <br> between Q and A — that's only added by
	// wpautop() when WordPress renders the content for display.
	$content = wpautop( $content );

	// Isolate the FAQ section: from <h2>FAQ</h2> up to the next <h2> (or end).
	if ( ! preg_match( '/<h2>\s*FAQ\s*<\/h2>(.*?)(?=<h2|$)/is', $content, $section ) ) {
		return array();
	}

	$faqs = array();
	if ( preg_match_all( '/<p>\s*<strong>(.*?)<\/strong>\s*<br\s*\/?>\s*(.*?)<\/p>/is', $section[1], $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $m ) {
			$q = wp_strip_all_tags( $m[1] );
			$a = wp_strip_all_tags( $m[2] );
			if ( $q && $a ) {
				$faqs[] = array( 'q' => $q, 'a' => $a );
			}
		}
	}
	return $faqs;
}
