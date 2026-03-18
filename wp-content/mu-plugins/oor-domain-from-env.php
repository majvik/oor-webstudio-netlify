<?php
/**
 * Plugin Name: OOR Domain from environment
 * Description: Подставляет домен из переменных WP_HOME и WP_SITEURL (для деплоя на другой домен без правки БД).
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$home_url = getenv( 'WP_HOME' );
$site_url = getenv( 'WP_SITEURL' );

// Apply env override only when it matches the current request host.
// This prevents accidental redirects when the platform still has old env values
// (or when multiple domains point to the same app during migration).
$request_host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( trim( $_SERVER['HTTP_HOST'] ) ) : '';

$home_host = '';
if ( $home_url !== false && $home_url !== '' ) {
	$parsed = wp_parse_url( $home_url );
	if ( is_array( $parsed ) && ! empty( $parsed['host'] ) ) {
		$home_host = strtolower( $parsed['host'] );
	}
}

$site_host = '';
if ( $site_url !== false && $site_url !== '' ) {
	$parsed = wp_parse_url( $site_url );
	if ( is_array( $parsed ) && ! empty( $parsed['host'] ) ) {
		$site_host = strtolower( $parsed['host'] );
	}
}

if ( $home_url !== false && $home_url !== '' && ( $request_host === '' || $home_host === '' || $home_host === $request_host ) ) {
	add_filter( 'pre_option_home', function () use ( $home_url ) {
		return $home_url;
	} );
}

if ( $site_url !== false && $site_url !== '' && ( $request_host === '' || $site_host === '' || $site_host === $request_host ) ) {
	add_filter( 'pre_option_siteurl', function () use ( $site_url ) {
		return $site_url;
	} );
} elseif ( $home_url !== false && $home_url !== '' && ( $request_host === '' || $home_host === '' || $home_host === $request_host ) ) {
	add_filter( 'pre_option_siteurl', function () use ( $home_url ) {
		return $home_url;
	} );
}
