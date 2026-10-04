<?php
/**
 * Plugin-aware SEO meta: Rank Math > Yoast > theme fallback postmeta > post title/excerpt.
 * Skips the theme's own <title>/OG/Twitter output when a known SEO plugin is active,
 * to avoid duplicate tags. Canonical + sitewide JSON-LD always runs (see inc/schema.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function craftypuns_seo_plugin_active() {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' );
}

function craftypuns_get_seo_title() {
	$post_id = get_queried_object_id();

	if ( defined( 'RANK_MATH_VERSION' ) ) {
		$title = get_post_meta( $post_id, 'rank_math_title', true );
		if ( $title ) {
			return $title;
		}
	}

	if ( defined( 'WPSEO_VERSION' ) ) {
		$title = get_post_meta( $post_id, '_yoast_wpseo_title', true );
		if ( $title ) {
			return $title;
		}
	}

	$title = get_post_meta( $post_id, '_seo_title', true );
	if ( $title ) {
		return $title;
	}

	if ( is_singular() ) {
		return get_the_title( $post_id );
	}

	return get_bloginfo( 'name' );
}

function craftypuns_get_seo_description() {
	$post_id = get_queried_object_id();

	if ( defined( 'RANK_MATH_VERSION' ) ) {
		$desc = get_post_meta( $post_id, 'rank_math_description', true );
		if ( $desc ) {
			return $desc;
		}
	}

	if ( defined( 'WPSEO_VERSION' ) ) {
		$desc = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		if ( $desc ) {
			return $desc;
		}
	}

	$desc = get_post_meta( $post_id, '_seo_description', true );
	if ( $desc ) {
		return $desc;
	}

	if ( is_singular() ) {
		return wp_strip_all_tags( get_the_excerpt( $post_id ) );
	}

	return get_bloginfo( 'description' );
}

/**
 * Outputs the theme's own <title>/meta description/OG/Twitter tags —
 * only when no recognized SEO plugin is active.
 */
function craftypuns_head_meta() {
	if ( craftypuns_seo_plugin_active() ) {
		// Rank Math / Yoast already output title, meta description, canonical, OG and Twitter tags.
		return;
	}

	$title = craftypuns_get_seo_title();
	$desc  = craftypuns_get_seo_description();
	$url   = craftypuns_get_canonical_url();

	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
	printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:type" content="%s" />' . "\n", is_singular( 'post' ) ? 'article' : 'website' );
	printf( '<meta name="twitter:card" content="summary_large_image" />' . "\n" );
	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );
}
add_action( 'wp_head', 'craftypuns_head_meta', 1 );

/**
 * Canonical URL is always emitted by the theme if no SEO plugin handles it.
 */
function craftypuns_get_canonical_url() {
	if ( is_singular() ) {
		return get_permalink();
	}
	if ( is_category() || is_tax() ) {
		return get_term_link( get_queried_object() );
	}
	global $wp;
	return home_url( add_query_arg( array(), $wp->request ) );
}
