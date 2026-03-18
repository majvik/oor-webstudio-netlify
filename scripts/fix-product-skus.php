<?php
/**
 * Fix missing product SKUs.
 *
 * Run inside WordPress container:
 *   docker exec oor-wordpress bash -c "wp --allow-root eval-file /var/www/html/wp-content/../scripts/fix-product-skus.php"
 *
 * OR from host (scripts is NOT mounted; copy first):
 *   docker cp scripts/fix-product-skus.php oor-wordpress:/tmp/fix-product-skus.php
 *   docker exec oor-wordpress wp --allow-root eval-file /tmp/fix-product-skus.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit( "Run via: wp --allow-root eval-file <this-file>\n" );
}

$dry_run = ! empty( getenv( 'DRY_RUN' ) );

if ( $dry_run ) {
    WP_CLI::log( "=== DRY RUN — no changes will be written ===\n" );
}

$products = wc_get_products( [
    'status' => 'publish',
    'limit'  => -1,
    'type'   => 'variable',
] );

$simple_products = wc_get_products( [
    'status' => 'publish',
    'limit'  => -1,
    'type'   => 'simple',
] );

$all_products = array_merge( $products, $simple_products );

$updated = 0;

foreach ( $all_products as $product ) {
    $pid   = $product->get_id();
    $title = $product->get_name();

    if ( $product->is_type( 'simple' ) ) {
        $sku = $product->get_sku();
        if ( empty( $sku ) ) {
            WP_CLI::warning( "Simple product #{$pid} «{$title}» has no SKU — skipping (set manually)" );
        } else {
            WP_CLI::log( "Simple #{$pid} «{$title}» SKU: {$sku} ✓" );
        }
        continue;
    }

    $variations = $product->get_children();
    if ( empty( $variations ) ) {
        WP_CLI::warning( "Variable product #{$pid} «{$title}» has no variations — skipping" );
        continue;
    }

    $first_var = wc_get_product( $variations[0] );
    if ( ! $first_var ) {
        continue;
    }

    $base_sku = $first_var->get_sku();
    if ( empty( $base_sku ) ) {
        WP_CLI::warning( "Product #{$pid} «{$title}»: first variation has no SKU — skipping (set at least one variation SKU first)" );
        continue;
    }

    $base = preg_replace( '/-BASIC$/i', '', $base_sku );

    $all_sizes = [];
    foreach ( $variations as $vid ) {
        $v = wc_get_product( $vid );
        if ( ! $v ) continue;
        $attrs = $v->get_attributes();
        $size  = reset( $attrs );
        $all_sizes[ $vid ] = $size ?: '';
    }

    $one_size_values = [ 'б/р', '' ];
    $all_one_size    = true;
    foreach ( $all_sizes as $s ) {
        if ( ! in_array( mb_strtolower( trim( $s ) ), $one_size_values, true ) ) {
            $all_one_size = false;
            break;
        }
    }

    $parent_sku = $product->get_sku();
    if ( empty( $parent_sku ) ) {
        $new_parent_sku = $base;
        WP_CLI::log( "  Product #{$pid} «{$title}»: set parent SKU → {$new_parent_sku}" );
        if ( ! $dry_run ) {
            update_post_meta( $pid, '_sku', $new_parent_sku );
        }
        $updated++;
    } else {
        WP_CLI::log( "  Product #{$pid} «{$title}»: parent SKU = {$parent_sku} ✓" );
        $base = preg_replace( '/-BASIC$/i', '', $parent_sku );
    }

    foreach ( $all_sizes as $vid => $size ) {
        $v       = wc_get_product( $vid );
        $cur_sku = $v->get_sku();

        if ( $all_one_size ) {
            $target_sku = $base;
        } else {
            $size_suffix = mb_strtoupper( trim( $size ) );
            $size_suffix = str_replace( [ 'Б/Р', '/' ], [ 'UNI', '-' ], $size_suffix );
            if ( empty( $size_suffix ) ) {
                $size_suffix = 'UNI';
            }
            $target_sku  = $base . '-' . $size_suffix;
        }

        if ( $cur_sku === $target_sku ) {
            WP_CLI::log( "    Variation #{$vid} ({$size}): SKU = {$cur_sku} ✓" );
            continue;
        }

        WP_CLI::log( "    Variation #{$vid} ({$size}): " . ( $cur_sku ? "{$cur_sku} → " : '' ) . "{$target_sku}" );
        if ( ! $dry_run ) {
            update_post_meta( $vid, '_sku', $target_sku );
        }
        $updated++;
    }
}

if ( ! $dry_run && $updated > 0 ) {
    WP_CLI::log( "\nRegenerating product lookup table..." );
    if ( function_exists( 'wc_update_product_lookup_tables' ) ) {
        wc_update_product_lookup_tables();
    }
}

$mode = $dry_run ? '(dry run)' : '';
WP_CLI::success( "Done {$mode}. Updated: {$updated} SKU entries." );
