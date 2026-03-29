<?php
/**
 * Промо-страница трека (без header/footer).
 *
 * @package OOR_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$ctx = isset($GLOBALS['oor_promo_context']) && is_array($GLOBALS['oor_promo_context'])
    ? $GLOBALS['oor_promo_context']
    : oor_promo_resolve_track();

$artist = isset($ctx['artist']) ? $ctx['artist'] : null;
$track  = isset($ctx['track']) ? $ctx['track'] : null;

if (!$artist || !$track) {
    wp_die(esc_html__('Страница не найдена.', 'oor-theme'), '', array('response' => 404));
}

$artist_name = get_the_title($artist);
$track_name  = isset($track['track_name']) ? (string) $track['track_name'] : '';

$cover_url = '';
$cover_id  = null;
$tc        = isset($track['track_cover']) ? $track['track_cover'] : null;
if (is_array($tc)) {
    if (!empty($tc['ID'])) {
        $cover_id = (int) $tc['ID'];
    }
    if (!empty($tc['url'])) {
        $cover_url = $tc['url'];
    }
} elseif (is_numeric($tc)) {
    $cover_id = (int) $tc;
}
if ($cover_id && !$cover_url) {
    $cover_url = wp_get_attachment_image_url($cover_id, 'full') ?: '';
}

$social_rows = isset($track['track_promo_social_links']) && is_array($track['track_promo_social_links'])
    ? $track['track_promo_social_links']
    : array();
$assets      = oor_promo_social_assets();

?><!DOCTYPE html>
<html class="oor-promo-page" <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $oor_promo_favicon = get_template_directory_uri() . '/public/assets/logo.svg';
    ?>
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url($oor_promo_favicon); ?>">
    <link rel="alternate icon" href="<?php echo esc_url($oor_promo_favicon); ?>">
    <title><?php echo esc_html($track_name ? $track_name . ' — ' . $artist_name : $artist_name); ?> | <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body class="oor-promo-page">
<div class="oor-promo-root">
    <?php if ($cover_url) : ?>
        <div class="oor-promo-bg" aria-hidden="true">
            <img class="oor-promo-bg-img" src="<?php echo esc_url($cover_url); ?>" alt="" decoding="async">
        </div>
    <?php endif; ?>
    <div class="oor-promo-blur-layer" aria-hidden="true"></div>
    <div class="oor-promo-texture-layer" aria-hidden="true"></div>

    <div class="oor-promo-card-wrap">
        <div class="oor-promo-card">
            <?php if ($cover_url) : ?>
                <div class="oor-promo-card-cover">
                    <img src="<?php echo esc_url($cover_url); ?>" alt="<?php echo esc_attr($track_name); ?>" decoding="async">
                </div>
            <?php endif; ?>
            <div class="oor-promo-card-meta">
                <p class="oor-promo-card-artist"><?php echo esc_html(strtoupper($artist_name)); ?></p>
                <?php if ($track_name !== '') : ?>
                    <p class="oor-promo-card-title"><?php echo esc_html($track_name); ?></p>
                <?php endif; ?>
            </div>
            <div class="oor-promo-card-links">
                <?php
                foreach ($social_rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $url = isset($row['url']) ? trim((string) $row['url']) : '';
                    if ($url === '') {
                        continue;
                    }
                    $platform = isset($row['platform']) ? (string) $row['platform'] : '';
                    $info     = isset($assets[ $platform ]) ? $assets[ $platform ] : null;
                    $label    = $info ? $info['label'] : ucwords(str_replace('_', ' ', $platform));
                    $icon     = $info ? $info['icon'] : '';
                    ?>
                    <a class="oor-promo-social-row" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                        <span class="oor-promo-social-left">
                            <?php if ($icon) : ?>
                                <span class="oor-promo-social-icon"><img src="<?php echo esc_url($icon); ?>" alt="" width="24" height="24" decoding="async"></span>
                            <?php endif; ?>
                            <span class="oor-promo-social-label"><?php echo esc_html($label); ?></span>
                        </span>
                        <span class="oor-promo-social-cta"><?php esc_html_e('Слушать', 'oor-theme'); ?></span>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php wp_footer(); ?>
</body>
</html>
