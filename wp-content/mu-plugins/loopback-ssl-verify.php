<?php
/**
 * Plugin Name: Loopback SSL verify fix
 * Description: Отключает проверку SSL для петлевых запросов. Локально: перенаправляет loopback на nginx:8443 и авторизует его (REST API context=edit иначе 403).
 * Version: 1.2
 */

if ( ! defined( 'OOR_LOOPBACK_SECRET' ) ) {
	define( 'OOR_LOOPBACK_SECRET', 'oor-loopback-' . ( defined( 'AUTH_KEY' ) ? AUTH_KEY : 'local' ) );
}

// Отключить проверку SSL для запросов к своему же хосту (self-signed в Docker).
add_filter( 'http_request_args', function ( $args, $url ) {
	$home = get_option( 'home' );
	if ( ! $home ) {
		return $args;
	}
	$home_host = wp_parse_url( $home, PHP_URL_HOST );
	$url_host  = wp_parse_url( $url, PHP_URL_HOST );
	if ( $home_host && $url_host && strtolower( $home_host ) === strtolower( $url_host ) ) {
		$args['sslverify'] = false;
	}
	return $args;
}, 10, 2 );

// Внутренний loopback без cookies → REST API возвращает 403 для context=edit. Выдаём первого админа по заголовку.
add_filter( 'determine_current_user', function ( $user_id ) {
	if ( $user_id ) {
		return $user_id;
	}
	if ( empty( $_SERVER['HTTP_X_OOR_LOOPBACK'] ) || $_SERVER['HTTP_X_OOR_LOOPBACK'] !== OOR_LOOPBACK_SECRET ) {
		return $user_id;
	}
	$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'orderby' => 'ID' ) );
	if ( ! empty( $admins ) ) {
		return (int) $admins[0]->ID;
	}
	return $user_id;
}, 1 );

// Локально: loopback на localhost:8443 из контейнера идёт на ::1 и не доходит. Перенаправить на nginx:8443 + заголовок для авторизации.
add_filter( 'pre_http_request', function ( $response, $args, $url ) {
	static $internal = false;
	if ( $internal ) {
		return $response;
	}
	$home = get_option( 'home' );
	if ( ! $home ) {
		return $response;
	}
	$parsed      = wp_parse_url( $url );
	$home_parsed = wp_parse_url( $home );
	if ( empty( $parsed['host'] ) || empty( $home_parsed['host'] ) || strtolower( $parsed['host'] ) !== strtolower( $home_parsed['host'] ) ) {
		return $response;
	}
	$internal    = true;
	$path        = isset( $parsed['path'] ) ? $parsed['path'] : '/';
	$query       = isset( $parsed['query'] ) ? '?' . $parsed['query'] : '';
	$loopback_url = 'https://nginx:8443' . $path . $query;
	$args['sslverify'] = false;
	if ( ! isset( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
		$args['headers'] = array();
	}
	$args['headers']['X-OOR-Loopback'] = OOR_LOOPBACK_SECRET;
	$response    = wp_remote_request( $loopback_url, $args );
	$internal    = false;
	return $response;
}, 10, 3 );
