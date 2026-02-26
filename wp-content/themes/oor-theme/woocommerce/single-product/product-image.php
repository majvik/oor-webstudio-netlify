<?php
/**
 * Single Product Image
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

use Automattic\WooCommerce\Enums\ProductType;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$gallery_ids       = $product->get_gallery_image_ids();
$has_gallery        = ! empty( $gallery_ids );
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);
?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
	<div class="woocommerce-product-gallery__wrapper">
		<?php
		if ( $post_thumbnail_id ) {
			if ( $has_gallery ) {
				$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
			} else {
				$full_src = wp_get_attachment_image_url( $post_thumbnail_id, 'full' );
				$full_img = wp_get_attachment_image( $post_thumbnail_id, 'full', false, array(
					'class'           => 'wp-post-image',
					'data-large_image' => $full_src,
					'data-large_image_width'  => '',
					'data-large_image_height' => '',
				) );
				$html = '<div class="woocommerce-product-gallery__image woocommerce-product-gallery__image--single">'
					. '<a href="' . esc_url( $full_src ) . '">' . $full_img . '</a>'
					. '</div>';
			}
		} else {
			$wrapper_classname = $product->is_type( ProductType::VARIABLE ) && ! empty( $product->get_available_variations( 'image' ) ) ?
				'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
				'woocommerce-product-gallery__image--placeholder';
			$html              = sprintf( '<div class="%s">', esc_attr( $wrapper_classname ) );
			$html             .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html             .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( $has_gallery ) {
			do_action( 'woocommerce_product_thumbnails' );
		}
		?>
	</div>
</div>
