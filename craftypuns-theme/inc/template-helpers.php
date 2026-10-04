<?php
/**
 * Small template helpers shared across header/category/single templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders a visible breadcrumb trail (Home > Hub > Sub-hub > Title).
 * Mirrors the structure emitted as BreadcrumbList JSON-LD in inc/schema.php.
 */
function craftypuns_breadcrumbs() {
	$trail = array(
		array( 'label' => __( 'Home', 'craftypuns' ), 'url' => home_url( '/' ) ),
	);

	if ( is_singular( 'post' ) ) {
		$cats = get_the_category();
		if ( $cats ) {
			$cat = $cats[0];
			if ( $cat->parent ) {
				$parent  = get_category( $cat->parent );
				$trail[] = array( 'label' => $parent->name, 'url' => get_term_link( $parent ) );
			}
			$trail[] = array( 'label' => $cat->name, 'url' => get_term_link( $cat ) );
		}
		$trail[] = array( 'label' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_category() ) {
		$term = get_queried_object();
		if ( $term->parent ) {
			$parent  = get_category( $term->parent );
			$trail[] = array( 'label' => $parent->name, 'url' => get_term_link( $parent ) );
		}
		$trail[] = array( 'label' => $term->name, 'url' => get_term_link( $term ) );
	} elseif ( is_page() ) {
		$trail[] = array( 'label' => get_the_title(), 'url' => get_permalink() );
	}

	echo '<nav class="text-sm text-slate-500" aria-label="' . esc_attr__( 'Breadcrumb', 'craftypuns' ) . '"><ol class="flex flex-wrap items-center gap-1">';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		echo '<li class="flex items-center gap-1">';
		if ( $i === $last ) {
			echo '<span class="text-slate-700" aria-current="page">' . esc_html( $crumb['label'] ) . '</span>';
		} else {
			echo '<a class="hover:text-amber-600" href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['label'] ) . '</a>';
			echo '<span class="text-slate-300">/</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * Reads the _bridge_links postmeta (serialized [{label, slug}, ...] set by the WXR import)
 * and returns it as a plain array.
 */
function craftypuns_get_bridge_links( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$links   = get_post_meta( $post_id, '_bridge_links', true );

	if ( empty( $links ) || ! is_array( $links ) ) {
		return array();
	}

	return $links;
}
