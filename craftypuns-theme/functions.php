<?php
/**
 * Crafty Puns theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CRAFTYPUNS_VERSION', '1.0.0' );
define( 'CRAFTYPUNS_DIR', get_template_directory() );
define( 'CRAFTYPUNS_URI', get_template_directory_uri() );

/**
 * Show 12 articles per page on category/hub archives, matching the 3-column
 * card grid (4 full rows) instead of WordPress's default of 10.
 */
function craftypuns_category_posts_per_page( $query ) {
	if ( ! is_admin() && $query->is_main_query() && is_category() ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'craftypuns_category_posts_per_page' );

/**
 * Expose _seo_title / _seo_description postmeta to the REST API so they can
 * be set programmatically and are picked up by craftypuns_get_seo_title()/
 * craftypuns_get_seo_description() in inc/seo-meta.php.
 */
function craftypuns_register_seo_meta() {
	foreach ( array( 'post', 'page' ) as $post_type ) {
		register_post_meta( $post_type, '_seo_title', array(
			'type'          => 'string',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
		register_post_meta( $post_type, '_seo_description', array(
			'type'          => 'string',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}
add_action( 'init', 'craftypuns_register_seo_meta' );

/**
 * Theme supports + nav menu registration.
 */
function craftypuns_setup() {
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 180,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'craftypuns' ),
		'footer'  => __( 'Footer Menu', 'craftypuns' ),
	) );
}
add_action( 'after_setup_theme', 'craftypuns_setup' );

/**
 * Enqueue compiled CSS/JS. Never enqueue src/tailwind.css directly —
 * assets/css/style.css is the npm run build output.
 */
function craftypuns_assets() {
	$style_path = CRAFTYPUNS_DIR . '/assets/css/style.css';
	$style_ver  = file_exists( $style_path ) ? filemtime( $style_path ) : CRAFTYPUNS_VERSION;
	wp_enqueue_style( 'craftypuns-style', CRAFTYPUNS_URI . '/assets/css/style.css', array(), $style_ver );

	$script_path = CRAFTYPUNS_DIR . '/assets/js/main.js';
	$script_ver  = file_exists( $script_path ) ? filemtime( $script_path ) : CRAFTYPUNS_VERSION;
	wp_enqueue_script( 'craftypuns-main', CRAFTYPUNS_URI . '/assets/js/main.js', array(), $script_ver, true );

	if ( is_page_template( 'page-templates/page-pun-generator.php' ) ) {
		$data_path = CRAFTYPUNS_DIR . '/assets/data/puns-dataset.json';
		$data_ver  = file_exists( $data_path ) ? filemtime( $data_path ) : CRAFTYPUNS_VERSION;
		$gen_path  = CRAFTYPUNS_DIR . '/assets/js/pun-generator.js';
		$gen_ver   = file_exists( $gen_path ) ? filemtime( $gen_path ) : CRAFTYPUNS_VERSION;

		wp_enqueue_script( 'craftypuns-pun-generator', CRAFTYPUNS_URI . '/assets/js/pun-generator.js', array( 'craftypuns-main' ), $gen_ver, true );
		wp_add_inline_script(
			'craftypuns-pun-generator',
			'window.craftypunsData = ' . wp_json_encode( array(
				'punsUrl'   => CRAFTYPUNS_URI . '/assets/data/puns-dataset.json?v=' . $data_ver,
				'aiPunUrl'  => esc_url_raw( rest_url( 'craftypuns/v1/generate-pun' ) ),
			) ) . ';',
			'before'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'craftypuns_assets' );

/**
 * Fallback menus render the exact structure from the Header & Footer sheet
 * as plain HTML, so nav works before menus are assigned in WP Admin.
 */
require_once CRAFTYPUNS_DIR . '/inc/fallback-menus.php';
require_once CRAFTYPUNS_DIR . '/inc/seo-meta.php';
require_once CRAFTYPUNS_DIR . '/inc/schema.php';
require_once CRAFTYPUNS_DIR . '/inc/template-helpers.php';

/**
 * Register the Pun Generator shortcode as a lightweight placeholder link,
 * since the generator itself is a separate existing tool.
 */
function craftypuns_pun_generator_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'topic' => '' ), $atts );
	$url   = home_url( '/pun-generator/' );
	if ( ! empty( $atts['topic'] ) ) {
		$url = add_query_arg( 'topic', sanitize_title( $atts['topic'] ), $url );
	}
	$label = ! empty( $atts['topic'] )
		? sprintf( __( 'Make Your Own %s Pun', 'craftypuns' ), esc_html( $atts['topic'] ) )
		: __( 'Try the Pun Generator', 'craftypuns' );

	return sprintf(
		'<a class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3 font-semibold text-slate-900 transition hover:bg-amber-300" href="%s">%s</a>',
		esc_url( $url ),
		esc_html( $label )
	);
}
add_shortcode( 'pun_generator', 'craftypuns_pun_generator_shortcode' );

/**
 * Excerpt length used on category/archive listings.
 */
function craftypuns_excerpt_length( $length ) {
	return 24;
}
add_filter( 'excerpt_length', 'craftypuns_excerpt_length' );

/**
 * AI custom pun generation: a REST endpoint the Pun Generator page calls to
 * get a brand-new pun for any topic the visitor types in, via the Claude API.
 *
 * The API key is never sent to the browser — it only ever lives in this PHP
 * file, read from the CRAFTYPUNS_ANTHROPIC_API_KEY constant below. Fill that
 * in with a real key (starts with sk-ant-...) to turn this on; until then the
 * endpoint returns a friendly "not configured" error and the frontend shows
 * that message instead of failing silently.
 */

if ( ! defined( 'CRAFTYPUNS_ANTHROPIC_API_KEY' ) ) {
	define( 'CRAFTYPUNS_ANTHROPIC_API_KEY', '' );
}

define( 'CRAFTYPUNS_AI_RATE_LIMIT', 10 );
define( 'CRAFTYPUNS_AI_RATE_WINDOW', HOUR_IN_SECONDS );

function craftypuns_register_ai_pun_route() {
	register_rest_route(
		'craftypuns/v1',
		'/generate-pun',
		array(
			'methods'             => 'POST',
			'callback'            => 'craftypuns_handle_ai_pun_request',
			'permission_callback' => '__return_true',
			'args'                => array(
				'topic' => array(
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'craftypuns_register_ai_pun_route' );

function craftypuns_ai_rate_limit_check() {
	$ip  = craftypuns_get_client_ip();
	$key = 'craftypuns_ai_rl_' . md5( $ip );
	$count = (int) get_transient( $key );

	if ( $count >= CRAFTYPUNS_AI_RATE_LIMIT ) {
		return false;
	}

	if ( 0 === $count ) {
		set_transient( $key, 1, CRAFTYPUNS_AI_RATE_WINDOW );
	} else {
		set_transient( $key, $count + 1, CRAFTYPUNS_AI_RATE_WINDOW );
	}

	return true;
}

function craftypuns_get_client_ip() {
	foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $header ) {
		if ( ! empty( $_SERVER[ $header ] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );
			$ip = trim( explode( ',', $ip )[0] );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
	}
	return '0.0.0.0';
}

function craftypuns_handle_ai_pun_request( WP_REST_Request $request ) {
	if ( empty( CRAFTYPUNS_ANTHROPIC_API_KEY ) ) {
		return new WP_REST_Response(
			array( 'error' => 'ai_not_configured', 'message' => 'AI pun generation isn\'t set up yet — an API key is needed.' ),
			503
		);
	}

	if ( ! craftypuns_ai_rate_limit_check() ) {
		return new WP_REST_Response(
			array( 'error' => 'rate_limited', 'message' => 'You\'ve hit the limit of ' . CRAFTYPUNS_AI_RATE_LIMIT . ' AI puns per hour. Try again later, or use the regular generator above.' ),
			429
		);
	}

	$topic = trim( $request->get_param( 'topic' ) );
	if ( '' === $topic || mb_strlen( $topic ) > 60 ) {
		return new WP_REST_Response(
			array( 'error' => 'invalid_topic', 'message' => 'Please enter a topic between 1 and 60 characters.' ),
			400
		);
	}

	$prompt = sprintf(
		'Write one single short, original, family-friendly pun about "%s". ' .
		'Reply with ONLY the pun text itself — no quotation marks, no preamble, no explanation, no extra puns.',
		$topic
	);

	$response = wp_remote_post(
		'https://api.anthropic.com/v1/messages',
		array(
			'timeout' => 20,
			'headers' => array(
				'content-type'      => 'application/json',
				'x-api-key'         => CRAFTYPUNS_ANTHROPIC_API_KEY,
				'anthropic-version' => '2023-06-01',
			),
			'body'    => wp_json_encode( array(
				'model'      => 'claude-haiku-4-5',
				'max_tokens' => 100,
				'messages'   => array(
					array( 'role' => 'user', 'content' => $prompt ),
				),
			) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_REST_Response(
			array( 'error' => 'upstream_error', 'message' => 'Could not reach the AI service. Please try again.' ),
			502
		);
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code || empty( $body['content'][0]['text'] ) ) {
		return new WP_REST_Response(
			array( 'error' => 'upstream_error', 'message' => 'The AI service did not return a pun. Please try again.' ),
			502
		);
	}

	$pun = trim( $body['content'][0]['text'] );
	$pun = trim( $pun, " \t\n\r\0\x0B\"'" );

	return new WP_REST_Response( array( 'pun' => $pun, 'topic' => $topic ), 200 );
}
