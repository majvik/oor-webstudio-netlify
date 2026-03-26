<?php
/**
 * Промо-страница трека: /promo/{artist_slug}/{track_promo_slug}/
 *
 * После деплоя или изменения правил: Сохранить «Постоянные ссылки» в админке
 * (Настройки → Постоянные ссылки → Сохранить) либо один раз вызвать flush_rewrite_rules().
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Проверка запроса промо (после parse_query).
 */
function oor_is_promo_track_request() {
    $a = get_query_var('oor_promo_artist', '');
    $t = get_query_var('oor_promo_track', '');
    return $a !== '' && $t !== '';
}

add_filter('query_vars', function ($vars) {
    $vars[] = 'oor_promo_artist';
    $vars[] = 'oor_promo_track';
    return $vars;
});

add_action('init', function () {
    add_rewrite_rule(
        '^promo/([^/]+)/([^/]+)/?$',
        'index.php?oor_promo_artist=$matches[1]&oor_promo_track=$matches[2]',
        'top'
    );
}, 20);

/**
 * Однократный flush при обновлении версии правил (после обновления темы).
 */
add_action('admin_init', function () {
    $ver = 2;
    $opt = (int) get_option('oor_promo_rewrite_rules_version', 0);
    if ($opt < $ver) {
        flush_rewrite_rules(false);
        update_option('oor_promo_rewrite_rules_version', $ver);
    }
});

add_filter('template_include', function ($template) {
    unset($GLOBALS['oor_promo_valid'], $GLOBALS['oor_promo_context']);
    if (!oor_is_promo_track_request()) {
        return $template;
    }
    $resolved = oor_promo_resolve_track();
    if (!$resolved['artist'] || !$resolved['track']) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        return get_query_template('404');
    }
    $GLOBALS['oor_promo_valid']  = true;
    $GLOBALS['oor_promo_context'] = $resolved;
    $custom = get_template_directory() . '/template-promo-track.php';
    return file_exists($custom) ? $custom : $template;
});

/**
 * noindex для промо-страницы.
 */
add_filter('wp_robots', function ($robots) {
    if (!empty($GLOBALS['oor_promo_valid'])) {
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
    }
    return $robots;
}, 20);

/**
 * Подписи и иконки соцсетей (иконки из Figma в public/assets/promo/).
 */
function oor_promo_social_assets() {
    $base = oor_theme_base_uri() . '/public/assets/promo/';
    return array(
        'apple_music'   => array('label' => 'Apple Music', 'icon' => $base . 'social-apple-music.svg'),
        'vk_music'      => array('label' => 'VK Музыка', 'icon' => $base . 'social-vk-music.svg'),
        'spotify'       => array('label' => 'Spotify', 'icon' => $base . 'social-spotify.svg'),
        'yandex_music'  => array('label' => 'Yandex Music', 'icon' => $base . 'social-yandex-music.svg'),
        'youtube'       => array('label' => 'YouTube', 'icon' => $base . 'social-youtube.svg'),
    );
}

/**
 * Найти пост artist и строку трека по slug промо.
 *
 * @return array{artist:\WP_Post|null,track:array|null,track_index:int}
 */
function oor_promo_resolve_track() {
    $artist_slug = sanitize_title((string) get_query_var('oor_promo_artist', ''));
    $track_slug  = sanitize_title((string) get_query_var('oor_promo_track', ''));
    if ($artist_slug === '' || $track_slug === '') {
        return array('artist' => null, 'track' => null, 'track_index' => -1);
    }
    $q = new WP_Query(array(
        'name'           => $artist_slug,
        'post_type'      => 'artist',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'no_found_rows'  => true,
    ));
    if (!$q->have_posts()) {
        wp_reset_postdata();
        return array('artist' => null, 'track' => null, 'track_index' => -1);
    }
    $artist = $q->posts[0];
    wp_reset_postdata();

    $tracks = function_exists('get_field') ? get_field('tracks', $artist->ID) : null;
    if (!is_array($tracks)) {
        return array('artist' => $artist, 'track' => null, 'track_index' => -1);
    }
    foreach ($tracks as $index => $row) {
        if (!is_array($row)) {
            continue;
        }
        $ps = isset($row['track_promo_slug']) ? sanitize_title((string) $row['track_promo_slug']) : '';
        if ($ps !== '' && $ps === $track_slug) {
            return array('artist' => $artist, 'track' => $row, 'track_index' => (int) $index);
        }
    }
    return array('artist' => $artist, 'track' => null, 'track_index' => -1);
}

/**
 * Автозаполнение track_promo_slug из track_name при сохранении (ACF).
 */
add_filter('acf/update_value/name=tracks', function ($value, $post_id, $field) {
    if (get_post_type($post_id) !== 'artist' || !is_array($value)) {
        return $value;
    }
    foreach ($value as $i => &$row) {
        if (!is_array($row)) {
            continue;
        }
        $slug = isset($row['track_promo_slug']) ? trim((string) $row['track_promo_slug']) : '';
        $name = isset($row['track_name']) ? trim((string) $row['track_name']) : '';
        if ($slug === '' && $name !== '') {
            $base = sanitize_title($name);
            if ($base === '') {
                $base = 'track';
            }
            $row['track_promo_slug'] = oor_promo_unique_slug_among_rows($value, (int) $i, $base);
        }
    }
    unset($row);
    return $value;
}, 5, 3);

/**
 * @param array $rows  Все строки repeater tracks.
 * @param int   $skip_index Индекс текущей строки (не сравнивать с собой).
 */
function oor_promo_unique_slug_among_rows(array $rows, $skip_index, $base) {
    $used = array();
    foreach ($rows as $j => $row) {
        if ((int) $j === (int) $skip_index || !is_array($row)) {
            continue;
        }
        if (!empty($row['track_promo_slug'])) {
            $used[] = sanitize_title((string) $row['track_promo_slug']);
        }
    }
    $candidate = sanitize_title($base);
    if ($candidate === '') {
        $candidate = 'track';
    }
    $n = 2;
    $base_s = sanitize_title($base);
    if ($base_s === '') {
        $base_s = 'track';
    }
    while (in_array($candidate, $used, true)) {
        $candidate = $base_s . '-' . $n;
        $n++;
    }
    return $candidate;
}

/**
 * На промо: без курсора; только нужные стили + скрипты.
 */
add_action('wp_enqueue_scripts', function () {
    if (empty($GLOBALS['oor_promo_valid'])) {
        return;
    }
    wp_dequeue_style('oor-cursor');
    wp_dequeue_script('oor-cursor');
    wp_dequeue_script('oor-scrollbar');
    wp_dequeue_script('oor-slider');
    wp_dequeue_script('oor-mobile-slider');
    wp_dequeue_script('oor-mobile-menu');
    wp_dequeue_script('oor-menu-sync');
    wp_dequeue_script('oor-artist-page');
    wp_dequeue_script('oor-events-slider');
    wp_dequeue_script('oor-merch-filter');
    wp_dequeue_script('oor-merch-images');
    wp_dequeue_script('oor-rolling-text');
    wp_dequeue_script('oor-scale-container');
    wp_dequeue_script('oor-size-sync');
    wp_dequeue_script('oor-studio-equipment-accordion');
    wp_dequeue_script('oor-talk-show-parallax');
    wp_dequeue_script('oor-main');
    wp_dequeue_script('oor-preloader');
    wp_dequeue_script('oor-navigation');
    wp_dequeue_script('gsap');

    foreach (array('base', 'grid', 'layout', 'utilities', 'slider', 'scrollbar', 'animations', 'components', 'cursor') as $h) {
        wp_dequeue_style('oor-' . $h);
    }

    $theme_uri = get_template_directory_uri();
    $ver        = defined('OOR_THEME_VERSION') ? OOR_THEME_VERSION : '1.0';
    $css_path   = get_template_directory() . '/assets/css/promo-track.css';
    $css_ver    = file_exists($css_path) ? $ver . '.' . filemtime($css_path) : $ver;

    wp_enqueue_style('oor-promo-track', $theme_uri . '/assets/css/promo-track.css', array('oor-reset', 'oor-tokens', 'oor-fonts'), $css_ver);
}, 200);

/**
 * Редактирование артиста: под полем slug промо — полная ссылка и кнопка «Копировать».
 */
add_action('admin_footer', function () {
    if (!function_exists('get_current_screen')) {
        return;
    }
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'artist' || $screen->base !== 'post') {
        return;
    }

    $base = trailingslashit(home_url());
    $i18n = array(
        'label'      => 'Промо URL',
        'copy'       => 'Копировать',
        'copied'     => 'Скопировано',
        'needArtist' => 'Сохраните запись, чтобы в адресе появился slug артиста.',
        'needTrack'  => 'Введите slug промо.',
    );
    ?>
    <script>
    (function ($) {
        var BASE = <?php echo wp_json_encode($base); ?>;
        var I18N = <?php echo wp_json_encode($i18n); ?>;

        function artistSlug() {
            var el = document.getElementById('post_name');
            if (el && el.value) {
                return el.value;
            }
            var full = document.getElementById('editable-post-name-full');
            if (full && full.textContent) {
                return full.textContent.replace(/\s+/g, '').trim();
            }
            return '';
        }

        function slugify(s) {
            if (!s) {
                return '';
            }
            return s.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')
                .replace(/[^a-z0-9\-]/g, '');
        }

        function ensureWrap($field) {
            var $input = $field.find('input[type="text"]').first();
            if (!$input.length) {
                return null;
            }
            var $wrap = $field.find('.oor-promo-url-admin');
            if ($wrap.length) {
                return $wrap;
            }
            $wrap = $('<div class="oor-promo-url-admin" style="margin-top:10px;padding:8px 10px;background:#f6f7f7;border:1px solid #c3c4c7;border-radius:4px;max-width:100%;box-sizing:border-box;" />');
            var $label = $('<strong class="oor-promo-url-label" style="display:block;margin-bottom:4px;"></strong>').text(I18N.label);
            $wrap.append($label);
            var $code = $('<code class="oor-promo-url-full" style="display:block;word-break:break-all;font-size:12px;margin:0 0 8px;padding:6px 8px;background:#fff;border:1px solid #dcdcde;border-radius:3px;"></code>');
            var $btn = $('<button type="button" class="button button-small oor-promo-copy-url" />').text(I18N.copy);
            var $hint = $('<p class="description oor-promo-url-hint" style="margin-top:8px;margin-bottom:0;"></p>');
            $wrap.append($code, $btn, $hint);
            var $target = $field.find('.acf-input').first();
            if (!$target.length) {
                $target = $field;
            }
            $target.append($wrap);

            $btn.on('click', function () {
                var t = $.trim($code.text());
                if (!t) {
                    return;
                }
                var btn = this;

                function done() {
                    var orig = I18N.copy;
                    btn.textContent = I18N.copied;
                    setTimeout(function () {
                        btn.textContent = orig;
                    }, 1500);
                }

                function fallback() {
                    var ta = document.createElement('textarea');
                    ta.value = t;
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.focus();
                    ta.select();
                    try {
                        document.execCommand('copy');
                        done();
                    } catch (e) {}
                    document.body.removeChild(ta);
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(t).then(done).catch(fallback);
                } else {
                    fallback();
                }
            });

            return $wrap;
        }

        function updateField($field) {
            var $wrap = ensureWrap($field);
            if (!$wrap) {
                return;
            }
            var $code = $wrap.find('.oor-promo-url-full');
            var $hint = $wrap.find('.oor-promo-url-hint');
            var trackRaw = $field.find('input[type="text"]').first().val() || '';
            var a = artistSlug();
            var t = slugify(trackRaw);
            var url = (a && t) ? (BASE + 'promo/' + a + '/' + t + '/') : '';
            $code.text(url);
            if (url) {
                $hint.text('');
            } else if (!a) {
                $hint.text(I18N.needArtist);
            } else {
                $hint.text(I18N.needTrack);
            }
        }

        function mountAll() {
            $('.acf-field[data-name="track_promo_slug"]').each(function () {
                updateField($(this));
            });
        }

        $(function () {
            mountAll();
            $(document).on('input change', '.acf-field[data-name="track_promo_slug"] input[type="text"]', function () {
                var $f = $(this).closest('.acf-field[data-name="track_promo_slug"]');
                updateField($f);
            });
            $(document).on('input change', '#post_name', function () {
                mountAll();
            });
            $(document).on('click', '#edit-slug-buttons .save, #edit-slug-buttons .cancel', function () {
                setTimeout(mountAll, 150);
            });
        });

        if (typeof acf !== 'undefined') {
            acf.addAction('append', function ($el) {
                $el.find('.acf-field[data-name="track_promo_slug"]').each(function () {
                    updateField($(this));
                });
            });
            acf.addAction('ready', mountAll);
        }
    })(jQuery);
    </script>
    <?php
}, 99);
