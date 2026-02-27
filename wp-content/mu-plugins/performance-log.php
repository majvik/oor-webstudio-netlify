<?php
/**
 * Plugin Name: Performance Logger
 * Description: Логирует медленные страницы (>2s) в error_log. Удалить после диагностики.
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	return;
}

if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
	return;
}

$oor_perf_start = microtime( true );

register_shutdown_function( function () use ( $oor_perf_start ) {
	$elapsed = microtime( true ) - $oor_perf_start;
	if ( $elapsed < 2.0 ) {
		return;
	}

	$uri    = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '?';
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '?';

	$db_queries = 0;
	$db_time    = 0;
	if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES && isset( $GLOBALS['wpdb'] ) ) {
		$db_queries = count( $GLOBALS['wpdb']->queries );
		foreach ( $GLOBALS['wpdb']->queries as $q ) {
			$db_time += $q[1];
		}
	}

	$msg = sprintf(
		'[SLOW PAGE] %.2fs | %s %s | DB: %d queries %.2fs | Mem: %dMB',
		$elapsed,
		$method,
		$uri,
		$db_queries,
		$db_time,
		memory_get_peak_usage( true ) / 1048576
	);

	error_log( $msg );
} );
