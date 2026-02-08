<?php
/**
 * Plugin Name: Environment Master
 * Description: Автоматически чинит URL и отключает прод-сервисы локально (localhost = DEV).
 * Version: 1.0.0
 */

if ( defined( 'ABSPATH' ) && defined( 'WP_CLI' ) && WP_CLI ) {
	return;
}

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// Только для веб-запросов: определяем, локаль это или прод
$host = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
$is_local = ( strpos( $host, 'localhost' ) !== false );

if ( $is_local ) {
	// Схема и хост с портом (https://localhost:8443)
	$scheme = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' )
		|| ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' )
		? 'https'
		: 'http';
	$home = $scheme . '://' . $host;

	add_filter( 'pre_option_home', function () use ( $home ) { return $home; }, 1, 1 );
	add_filter( 'pre_option_siteurl', function () use ( $home ) { return $home; }, 1, 1 );

	// Отключаем индексацию поисковиков локально
	add_filter( 'pre_option_blog_public', '__return_zero' );
}
