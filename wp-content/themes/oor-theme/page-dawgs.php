<?php
/**
 * Template Name: DAWGS
 * DAWGS
 */

get_header();
$t = get_template_directory_uri();

$hero_subtitle = get_field('dawgs_hero_subtitle') ?: 'ИГРАТЬ И ПОМОГАТЬ';
$hero_text     = get_field('dawgs_hero_text') ?: 'Наша медийная баскетбольная команда DAWGS это один из главных фаворитов Лиги Ставок Media Basket и дважды играла в четвертьфинале Лиги. Лидеры мнений и известные личности играют под руководством легенды в мировом баскетболе — Андрея Кириленко';

$team_photo     = get_field('dawgs_team_photo');
$team_photo_url = !empty($team_photo['url']) ? $team_photo['url'] : $t . '/public/assets/dawgs-team-photo.png';

$coaches = get_field('dawgs_coaches');

$quote_text   = get_field('dawgs_quote_text') ?: '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean suscipit felis in tellus volutpat sodales in sed erat"';
$quote_author = get_field('dawgs_quote_author') ?: 'Цитата для лояльности';

$players_raw = get_field('dawgs_players');
if (!is_array($players_raw)) $players_raw = [];
usort($players_raw, function($a, $b) {
    return intval($a['order'] ?? 0) - intval($b['order'] ?? 0);
});
?>

<!-- HERO Section -->
    <section class="oor-section-hero oor-dawgs-hero">
        <div class="oor-container">
            <div class="oor-grid oor-dawgs-hero-grid">
                <div class="oor-col-7">
                    <h1 class="oor-dawgs-hero-title">DAWGS MOSCOW</h1>
                </div>
                <div class="oor-col-4 oor-dawgs-hero-description-wrapper">
                    <div class="oor-dawgs-hero-description">
                        <div class="oor-dawgs-hero-index">[01]</div>
                        <div class="oor-dawgs-hero-description-content">
                            <h2 class="oor-dawgs-hero-subtitle"><?php echo esc_html($hero_subtitle); ?></h2>
                            <p class="oor-dawgs-hero-text"><?php echo wp_kses_post($hero_text); ?></p>
                        </div>
                    </div>
                </div>
                <div class="oor-col-1 oor-dawgs-hero-plus-wrapper">
                    <div class="oor-dawgs-hero-plus">
                        <img src="<?php echo $t; ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="oor-dawgs-hero-gallery">
            <div class="oor-dawgs-hero-gallery-item oor-dawgs-gallery-1">
                <img src="<?php echo $t; ?>/public/assets/dawgs-gallery-1.png" alt="DAWGS">
            </div>
            <div class="oor-dawgs-hero-gallery-item oor-dawgs-gallery-2">
                <img src="<?php echo $t; ?>/public/assets/dawgs-gallery-2.png" alt="DAWGS">
            </div>
            <div class="oor-dawgs-hero-gallery-item oor-dawgs-gallery-3">
                <img src="<?php echo $t; ?>/public/assets/dawgs-gallery-3.png" alt="DAWGS">
            </div>
            <div class="oor-dawgs-hero-gallery-item oor-dawgs-gallery-4">
                <img src="<?php echo $t; ?>/public/assets/dawgs-gallery-4.png" alt="DAWGS">
            </div>
            <div class="oor-dawgs-hero-gallery-item oor-dawgs-gallery-5">
                <img src="<?php echo $t; ?>/public/assets/dawgs-gallery-5.png" alt="DAWGS">
            </div>
        </div>
        
        <div class="oor-container">
            <div class="oor-dawgs-hero-footer">
                <div class="oor-dawgs-hero-footer-left">
                    <div class="oor-dawgs-hero-stats">
                        <div class="oor-dawgs-hero-stats-number">32млн</div>
                        <div class="oor-dawgs-hero-stats-text">собрали для фондов «Кириленко — детям», «Дари Надежду»</div>
                    </div>
                </div>
                <div class="oor-dawgs-hero-footer-right">
                    <div class="oor-dawgs-hero-sponsors">
                        <div class="oor-dawgs-sponsor-1">
                            <img src="<?php echo $t; ?>/public/assets/hero-logo-1.svg" alt="Кириленко — детям">
                        </div>
                        <div class="oor-dawgs-sponsor-2">
                            <img src="<?php echo $t; ?>/public/assets/hero-logo-2.svg" alt="Дари Надежду">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="oor-dawgs-hero-line oor-dawgs-hero-line-left"></div>
        <div class="oor-dawgs-hero-line oor-dawgs-hero-line-right"></div>
    </section>

    <!-- Team Photo Section -->
    <section class="oor-dawgs-team-photo">
        <div class="oor-dawgs-team-photo-img">
            <img src="<?php echo esc_url($team_photo_url); ?>" alt="DAWGS Team" class="oor-media-cover">
        </div>
    </section>

    <!-- Coach & Ambassador Section -->
    <section class="oor-dawgs-coach">
        <div class="oor-container">
            <div class="oor-dawgs-coach-header">
                <h2 class="oor-dawgs-coach-title">Главный тренер и Амбасадор команды</h2>
            </div>
            
            <div class="oor-dawgs-coach-cards">
                <?php if (!empty($coaches) && is_array($coaches)) : ?>
                    <?php foreach ($coaches as $coach) :
                        $c_photo = !empty($coach['photo']['url']) ? $coach['photo']['url'] : '';
                        $c_name  = !empty($coach['name']) ? $coach['name'] : '';
                        $c_role  = !empty($coach['role']) ? $coach['role'] : '';
                        $c_layout = !empty($coach['layout']) ? $coach['layout'] : 'horizontal';
                        if (!$c_photo) continue;
                    ?>
                        <div class="oor-dawgs-coach-card oor-dawgs-coach-card--<?php echo esc_attr($c_layout); ?>">
                            <div class="oor-dawgs-coach-card-image">
                                <img src="<?php echo esc_url($c_photo); ?>" alt="<?php echo esc_attr($c_name); ?>" class="oor-media-cover">
                            </div>
                            <div class="oor-dawgs-coach-card-info">
                                <h3 class="oor-dawgs-player-name"><?php echo esc_html($c_name); ?></h3>
                                <p class="oor-dawgs-player-role"><?php echo wp_kses_post($c_role); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="oor-dawgs-coach-card oor-dawgs-coach-card--horizontal">
                        <div class="oor-dawgs-coach-card-image" style="width:448px;height:686px;">
                            <img src="<?php echo $t; ?>/public/assets/dawgs-coach-1.png" alt="Понкрашов Антон" class="oor-media-cover">
                        </div>
                        <div class="oor-dawgs-coach-card-info">
                            <h3 class="oor-dawgs-player-name">Понкрашов Антон Александрович</h3>
                            <p class="oor-dawgs-player-role">Профессиональный баскетболист. Играет на всех позициях и создает шоу</p>
                        </div>
                    </div>
                    <div class="oor-dawgs-coach-card oor-dawgs-coach-card--vertical">
                        <div class="oor-dawgs-coach-card-image" style="width:336px;height:504px;">
                            <img src="<?php echo $t; ?>/public/assets/dawgs-coach-2.png" alt="Меркулова Наталья" class="oor-media-cover">
                        </div>
                        <div class="oor-dawgs-coach-card-info">
                            <h3 class="oor-dawgs-player-name">Меркулова Наталья Сергеевна</h3>
                            <p class="oor-dawgs-player-role">Представитель международного проекта «Her world, her rules». Совместно с ФИБА и РФБ. Чемпионка Golf Gold Conference национального чемпионата США</p>
                            <p class="oor-dawgs-player-role">Участница и призер всероссийских и европейских чемпионатов. Первая девушка тренер в медиабаскете!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Quote -->
            <div class="oor-dawgs-quote">
                <blockquote class="oor-dawgs-quote-text"><?php echo esc_html($quote_text); ?></blockquote>
                <p class="oor-dawgs-quote-author"><?php echo esc_html($quote_author); ?></p>
            </div>
        </div>
    </section>

    <!-- Players Section (asymmetric grid) -->
    <section class="oor-dawgs-players">
        <div class="oor-container">
            <?php
            if (!empty($players_raw)) :
                /*
                 * Десктопная сетка повторяется циклами по 11 игроков (4 строки):
                 *   Строка 1 (row-1): 3 карточки — фото сверху, текст снизу
                 *   Строка 2 (row-2): 2 карточки — (img+text right), (text left+img)
                 *   Строка 3 (row-3): 3 карточки — below, right-compact, left
                 *   Строка 4 (row-4): 3 карточки — фото сверху, текст снизу
                 *
                 * На адаптиве (≤1439px) — простая плитка, сетка не важна.
                 */
                $chunks = array_chunk($players_raw, 11);
                $cycle = 0;

                foreach ($chunks as $chunk) :
                    $cycle++;
                    $row_patterns = [
                        ['count' => 3, 'row_class' => 'oor-dawgs-row-1', 'cards' => ['below', 'below', 'below']],
                        ['count' => 2, 'row_class' => 'oor-dawgs-row-2', 'cards' => ['right', 'left']],
                        ['count' => 3, 'row_class' => 'oor-dawgs-row-3', 'cards' => ['below', 'right-compact', 'left']],
                        ['count' => 3, 'row_class' => 'oor-dawgs-row-4', 'cards' => ['below', 'below', 'below']],
                    ];

                    $offset = 0;
                    $row_num = 0;

                    foreach ($row_patterns as $pattern) :
                        $row_players = array_slice($chunk, $offset, $pattern['count']);
                        if (empty($row_players)) break;
                        $offset += $pattern['count'];
                        $row_num++;
            ?>
            <div class="oor-dawgs-players-row <?php echo esc_attr($pattern['row_class']); ?>">
                <?php foreach ($row_players as $pi => $player) :
                    $p_photo = !empty($player['photo']['url']) ? $player['photo']['url'] : '';
                    $p_name  = !empty($player['name']) ? $player['name'] : '';
                    $p_role  = !empty($player['role']) ? $player['role'] : '';
                    if (!$p_photo) continue;

                    $card_type = isset($pattern['cards'][$pi]) ? $pattern['cards'][$pi] : 'below';
                    $is_left = ($card_type === 'left');
                ?>
                <div class="oor-dawgs-player-card oor-dawgs-player--<?php echo esc_attr($card_type); ?>">
                    <?php if ($is_left) : ?>
                    <div class="oor-dawgs-player-info oor-dawgs-player-info--right-align">
                        <h3 class="oor-dawgs-player-name"><?php echo esc_html($p_name); ?></h3>
                        <p class="oor-dawgs-player-role"><?php echo wp_kses_post($p_role); ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="oor-dawgs-player-img">
                        <img src="<?php echo esc_url($p_photo); ?>" alt="<?php echo esc_attr($p_name); ?>" class="oor-media-cover">
                    </div>
                    <?php if (!$is_left) : ?>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name"><?php echo esc_html($p_name); ?></h3>
                        <p class="oor-dawgs-player-role"><?php echo wp_kses_post($p_role); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php
                    endforeach;
                endforeach;

            else :
            ?>
            <!-- Fallback: hardcoded players -->
            <div class="oor-dawgs-players-row oor-dawgs-row-1">
                <div class="oor-dawgs-player-card oor-dawgs-player--below" style="width:303px;">
                    <div class="oor-dawgs-player-img" style="height:420px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-sk.png" alt="Крайнов Станислав" class="oor-media-cover">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Крайнов Станислав (SK)</h3>
                        <p class="oor-dawgs-player-role">Трехкратный чемпион и лучший игрок первого десятилетия чемпионата АСБ. Ведущий канала Взял Мяч. Баскетбольный комментатор Окко и Старт</p>
                    </div>
                </div>
                <div class="oor-dawgs-player-card oor-dawgs-player--below" style="width:362px;">
                    <div class="oor-dawgs-player-img" style="height:557px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-dimma.png" alt="Урих Дмитрий" class="oor-media-cover">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Урих Дмитрий Александрович (DIMMA URIH)</h3>
                        <p class="oor-dawgs-player-role">Артист лейбла Out Of Records</p>
                    </div>
                </div>
                <div class="oor-dawgs-player-card oor-dawgs-player--below" style="width:303px;">
                    <div class="oor-dawgs-player-img" style="height:420px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-chelak.png" alt="Челак Илья" class="oor-media-cover">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Челак Илья (CHELAK)</h3>
                        <p class="oor-dawgs-player-role">Семейный блогер, резидент Insight People, обладатель MVP и ММР</p>
                    </div>
                </div>
            </div>
            
            <div class="oor-dawgs-players-row oor-dawgs-row-2">
                <div class="oor-dawgs-player-card oor-dawgs-player--right">
                    <div class="oor-dawgs-player-img" style="width:340px;height:497px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-kirilenko.png" alt="Кириленко Андрей" class="oor-media-cover">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Кириленко Андрей (KIRILENKO)</h3>
                        <p class="oor-dawgs-player-role">Российский баскетболист и спортивный функционер. Заслуженный мастер спорта России. Президент Российской федерации баскетбола. Основатель фонда "Кириленко - детям!"</p>
                    </div>
                </div>
                <div class="oor-dawgs-player-card oor-dawgs-player--left">
                    <div class="oor-dawgs-player-info oor-dawgs-player-info--right-align">
                        <h3 class="oor-dawgs-player-name">Ильменков Павел (KEKS)</h3>
                        <p class="oor-dawgs-player-role">Бывший профессиональный баскетболист. Полуфиналист 2-ого сезона Медиа-Лиги ЛИГА СТАВОК. Блогер Keks Life</p>
                    </div>
                    <div class="oor-dawgs-player-img" style="width:303px;height:420px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-keks.png" alt="Ильменков Павел" class="oor-media-cover">
                    </div>
                </div>
            </div>
            
            <div class="oor-dawgs-players-row oor-dawgs-row-3">
                <div class="oor-dawgs-player-card oor-dawgs-player--below" style="width:303px;">
                    <div class="oor-dawgs-player-img" style="width:303px;height:342px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-gubanov.png" alt="Губанов Петр">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Губанов Петр (GUBANOV)</h3>
                        <p class="oor-dawgs-player-role">Бывший профессиональный баскетболист. Участник Матча</p>
                    </div>
                </div>
                <div class="oor-dawgs-player-card oor-dawgs-player--right-compact">
                    <div class="oor-dawgs-player-img" style="width:303px;height:420px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-baban.png" alt="Бабинцев Михаил">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Бабинцев Михаил (BABAN)</h3>
                        <p class="oor-dawgs-player-role">Баскетболист, баскетбольный блогер</p>
                    </div>
                </div>
                <div class="oor-dawgs-player-card oor-dawgs-player--left">
                    <div class="oor-dawgs-player-info oor-dawgs-player-info--right-align">
                        <h3 class="oor-dawgs-player-name">Рытенко Дмитрий (FLIP)</h3>
                        <p class="oor-dawgs-player-role">Профессиональный баскетболист по баскетболу 3х3. Чемпион России 3х3 2012. Неоднократный призер соревнований по баскетболу 3x3. Хороший человек и просто машина своего дела</p>
                    </div>
                    <div class="oor-dawgs-player-img" style="width:298px;height:459px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-flip.png" alt="Рытенко Дмитрий">
                    </div>
                </div>
            </div>
            
            <div class="oor-dawgs-players-row oor-dawgs-row-4">
                <div class="oor-dawgs-player-card oor-dawgs-player--below" style="width:303px;">
                    <div class="oor-dawgs-player-img" style="height:530px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-stock.png" alt="Гребельный Ярослав" class="oor-media-cover">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Гребельный Ярослав (STOCK)</h3>
                        <p class="oor-dawgs-player-role">Бывший профессиональный баскетболист</p>
                    </div>
                </div>
                <div class="oor-dawgs-player-card oor-dawgs-player--below" style="width:303px;">
                    <div class="oor-dawgs-player-img" style="height:341px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-redflag.png" alt="Алексеев Иван" class="oor-media-cover">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Алексеев Иван (Red_flag)</h3>
                        <p class="oor-dawgs-player-role">Я ругаю, фолю, бешу соперника, выражаю характер. Покажу, чего стою</p>
                    </div>
                </div>
                <div class="oor-dawgs-player-card oor-dawgs-player--below" style="width:303px;">
                    <div class="oor-dawgs-player-img" style="height:420px;">
                        <img src="<?php echo $t; ?>/public/assets/dawgs-player-mikhon.png" alt="Лагутин Михаил" class="oor-media-cover">
                    </div>
                    <div class="oor-dawgs-player-info">
                        <h3 class="oor-dawgs-player-name">Лагутин Михаил (MIKHON)</h3>
                        <p class="oor-dawgs-player-role">Многократный чемпион и MVP МЛБЛ дивизион Волгоград и АСБ дивизион Волгоград. Худая легенда</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="oor-dawgs-timeline">
        <div class="oor-dawgs-timeline-scroll">
            <div class="oor-dawgs-timeline-inner">
                <img src="<?php echo $t; ?>/public/assets/dawgs-timeline-1.svg" alt="Timeline - Main" class="oor-dawgs-timeline-img" width="2977" height="850">
                <img src="<?php echo $t; ?>/public/assets/dawgs-timeline-2.svg" alt="Timeline - Межсезонье" class="oor-dawgs-timeline-img" width="2977" height="850">
            </div>
        </div>
    </section>

<?php
get_footer();
?>
