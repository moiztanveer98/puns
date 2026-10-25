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
	if ( is_front_page() ) {
		return __( 'Crafty Puns - Free Pun Generator & 1,000+ Original Puns', 'craftypuns' );
	}

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
	if ( is_front_page() ) {
		return __( 'Browse 1,000+ original puns by topic or occasion, or generate your own instantly with our free Pun Generator. 100% original, always free, no sign-up.', 'craftypuns' );
	}

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
 * The image to use for og:image/twitter:image: the post's featured image on
 * singular posts, falling back to the site logo everywhere else (homepage,
 * category archives, pages) so link previews never show a blank thumbnail.
 */
function craftypuns_get_social_image() {
	if ( is_singular( 'post' ) && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
		if ( $src ) {
			return array(
				'url'    => $src[0],
				'width'  => $src[1],
				'height' => $src[2],
			);
		}
	}

	if ( has_custom_logo() ) {
		$logo_id  = get_theme_mod( 'custom_logo' );
		$logo_src = $logo_id ? wp_get_attachment_image_src( $logo_id, 'full' ) : false;
		if ( $logo_src ) {
			return array(
				'url'    => $logo_src[0],
				'width'  => $logo_src[1],
				'height' => $logo_src[2],
			);
		}
	}

	return null;
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
	$image = craftypuns_get_social_image();

	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
	printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( get_locale() ) );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:type" content="%s" />' . "\n", is_singular( 'post' ) ? 'article' : 'website' );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image['url'] ) );
		printf( '<meta property="og:image:width" content="%d" />' . "\n", (int) $image['width'] );
		printf( '<meta property="og:image:height" content="%d" />' . "\n", (int) $image['height'] );
	}
	printf( '<meta name="twitter:card" content="summary_large_image" />' . "\n" );
	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );
	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $image['url'] ) );
	}
}
add_action( 'wp_head', 'craftypuns_head_meta', 1 );

/**
 * Overrides WP core's title-tag output (which uses get_the_title()) with our
 * SEO title so <title> matches the meta_title set on each article/page.
 */
function craftypuns_document_title( $title ) {
	if ( craftypuns_seo_plugin_active() || ! ( is_singular() || is_front_page() ) ) {
		return $title;
	}
	return craftypuns_get_seo_title();
}
add_filter( 'pre_get_document_title', 'craftypuns_document_title' );

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
