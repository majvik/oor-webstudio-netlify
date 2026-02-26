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

if ( $home_url !== false && $home_url !== '' ) {
	add_filter( 'pre_option_home', function () use ( $home_url ) {
		return $home_url;
	} );
}

if ( $site_url !== false && $site_url !== '' ) {
	add_filter( 'pre_option_siteurl', function () use ( $site_url ) {
		return $site_url;
	} );
} elseif ( $home_url !== false && $home_url !== '' ) {
	add_filter( 'pre_option_siteurl', function () use ( $home_url ) {
		return $home_url;
	} );
}
