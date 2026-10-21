<?php
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fill in with a real Anthropic API key to enable AI pun generation.
if ( ! defined( 'CRAFTYPUNS_ANTHROPIC_API_KEY' ) ) {
	define( 'CRAFTYPUNS_ANTHROPIC_API_KEY', '' );
}

define( 'CRAFTYPUNS_AI_RATE_LIMIT', 10 );       // max requests...
define( 'CRAFTYPUNS_AI_RATE_WINDOW', HOUR_IN_SECONDS ); // ...per this window, per visitor.

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

/**
 * A simple per-visitor-IP rate limit using transients, so a public "generate
 * with AI" button can't be hammered into a large API bill.
 */
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
