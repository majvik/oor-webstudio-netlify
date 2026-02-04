<?php
/**
 * Template Name: Студия
 * Студия
 */

get_header();
?>

<!-- HERO Section -->
    <section class="oor-section-hero oor-studio-hero">
        <div class="oor-container">
            <div class="oor-grid oor-studio-hero-grid">
                <div class="oor-col-10">
                    <h1 class="oor-studio-hero-title">ПРОФЕССИОНАЛЬНАЯ СТУДИЯ ЗВУКОЗАПИСИ</h1>
                </div>
                <div class="oor-col-1"></div>
                <div class="oor-col-1">
                    <div class="oor-studio-hero-index">[01]</div>
                </div>
            </div>
        </div>
        
        <div class="oor-studio-hero-image">
            <picture>
                <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-hero-image.avif" type="image/avif">
                <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-hero-image.webp" type="image/webp">
                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-hero-image.png" alt="Студия Out of Records" class="oor-media-cover">
            </picture>
        </div>
    </section>

    <!-- Content Section [02] -->
    <section class="oor-studio-content-section">
        <div class="oor-container oor-studio-content-wrapper">
            <div class="oor-studio-content">
                <div class="oor-studio-content-left">
                    <div class="oor-studio-section-index">[02]</div>
                    <div class="oor-studio-content-title-wrapper">
                        <h2 class="oor-studio-content-title">
                            СТУДИЯ С ПРЕМИУМ АКУСТИКОЙ И ТОПОВЫМ ОБОРУДОВАНИЕМ
                        </h2>
                    </div>
                    <div class="oor-studio-content-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                    </div>
                </div>
                
                <div class="oor-studio-content-right">
                    <div class="oor-studio-image-row">
                        <div class="oor-studio-image-item">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-image-item-1.avif" type="image/avif">
                                <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-image-item-1.webp" type="image/webp">
                                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-image-item-1.png" alt="Студия" class="oor-media-cover">
                            </picture>
                        </div>
                        <div class="oor-studio-image-item">
                            <picture>
                                <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-image-item-2.avif" type="image/avif">
                                <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-image-item-2.webp" type="image/webp">
                                <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-image-item-2.png" alt="Студия" class="oor-media-cover">
                            </picture>
                        </div>
                    </div>
                    <p class="oor-studio-content-text">
                        Музыка попадает в сердца благодаря артисту, а не метрикам. Мы помогаем реализоваться в музыке без оглядки на цифры и алгоритмы — и остаться собой без страха непопулярности
                    </p>
                </div>
                
                <div class="oor-studio-plus-icons-group">
                    <div class="oor-studio-plus-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                    </div>
                    <div class="oor-studio-plus-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="18" height="18">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Studio Equipment Section [03] -->
    <section class="oor-studio-equipment-section">
        <div class="oor-container">
            <div class="oor-studio-equipment-header">
                <h2 class="oor-studio-equipment-title">ЧТО В СТУДИИ?</h2>
                <div class="oor-studio-section-index">[03]</div>
                <div class="oor-studio-equipment-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="25" height="18">
                </div>
            </div>
            
            <div class="oor-studio-equipment-wrapper">
                <div class="oor-studio-equipment-table">
                    <div
                        class="oor-studio-equipment-row oor-studio-equipment-row-active"
                        data-image-png="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.png"
                        data-image-webp="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.webp"
                        data-image-avif="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.avif"
                    >
                        <div class="oor-studio-equipment-index">01</div>
                        <div class="oor-studio-equipment-content">
                            <div class="oor-studio-equipment-header-row">
                                <h3 class="oor-studio-equipment-item-title">МОНИТОРИНГ И АКУСТИКА</h3>
                                <div class="oor-studio-equipment-item-icon oor-studio-equipment-item-icon-open">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/minus-icon.svg" alt="" width="17" height="17">
                                </div>
                            </div>
                            <p class="oor-studio-equipment-item-description">
                                PMC 8-2 XBD, GENELEC 8361A, Sonarworks SoundID Reference, Grace Design m905 и другие
                            </p>
                            <div class="oor-studio-equipment-item-image">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.png" alt="Мониторинг и акустика" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                    </div>
                    
                    <div
                        class="oor-studio-equipment-row"
                        data-image-png="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-2.png"
                        data-image-webp="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-2.webp"
                        data-image-avif="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-2.avif"
                    >
                        <div class="oor-studio-equipment-index">02</div>
                        <div class="oor-studio-equipment-content">
                            <div class="oor-studio-equipment-header-row">
                                <h3 class="oor-studio-equipment-item-title">РАБОЧИЕ СТАНЦИИ И УПРАВЛЕНИЕ</h3>
                                <div class="oor-studio-equipment-item-icon">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17">
                                </div>
                            </div>
                            <p class="oor-studio-equipment-item-description">
                                Цифровая микшерная консоль AVID S1 + Pro Tools | Dock и звуковая карта Universal Audio Apollo x8 для аналогового качества записи
                            </p>
                            <div class="oor-studio-equipment-item-image">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-2.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-2.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-2.png" alt="Рабочие станции и управление" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                    </div>
                    
                    <div
                        class="oor-studio-equipment-row"
                        data-image-png="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-3.png"
                        data-image-webp="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-3.webp"
                        data-image-avif="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-3.avif"
                    >
                        <div class="oor-studio-equipment-index">03</div>
                        <div class="oor-studio-equipment-content">
                            <div class="oor-studio-equipment-header-row">
                                <h3 class="oor-studio-equipment-item-title">МИКРОФОНЫ И ПРЕДУСИЛИТЕЛИ</h3>
                                <div class="oor-studio-equipment-item-icon">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17">
                                </div>
                            </div>
                            <p class="oor-studio-equipment-item-description">
                                Sony C-800G для хип-хоп и поп-вокала, Neumann U87 Ai с мягким и плотным тембром, Shure SM7B для речитатива, ламповый российский премиум-микрофон Soyuz 017 Tube, легендарные предусилители Neve 1073 и Avalon VT-737sp
                            </p>
                            <div class="oor-studio-equipment-item-image">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-3.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-3.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-3.png" alt="Микрофоны и предусилители" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                    </div>
                    
                    <div
                        class="oor-studio-equipment-row"
                        data-image-png="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-4.png"
                        data-image-webp="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-4.webp"
                        data-image-avif="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-4.avif"
                    >
                        <div class="oor-studio-equipment-index">04</div>
                        <div class="oor-studio-equipment-content">
                            <div class="oor-studio-equipment-header-row">
                                <h3 class="oor-studio-equipment-item-title">КОМПРЕССОРЫ И ЭКВАЛАЙЗЕРЫ</h3>
                                <div class="oor-studio-equipment-item-icon">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17">
                                </div>
                            </div>
                            <p class="oor-studio-equipment-item-description">
                                Вокальные компрессоры Tube-Tech CL1B и Voxbox, универсальный компрессор Empirical Labs Distressor EL8X, эквалайзеры API 5500 EQ и Pultec EQP-1A и другие
                            </p>
                            <div class="oor-studio-equipment-item-image">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-4.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-4.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-4.png" alt="Компрессоры и эквалайзеры" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                    </div>
                    
                    <div
                        class="oor-studio-equipment-row"
                        data-image-png="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-5.png"
                        data-image-webp="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-5.webp"
                        data-image-avif="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-5.avif"
                    >
                        <div class="oor-studio-equipment-index">05</div>
                        <div class="oor-studio-equipment-content">
                            <div class="oor-studio-equipment-header-row">
                                <h3 class="oor-studio-equipment-item-title">ИНСТРУМЕНТЫ И КОНТРОЛЛЕРЫ</h3>
                                <div class="oor-studio-equipment-item-icon">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17">
                                </div>
                            </div>
                            <p class="oor-studio-equipment-item-description">
                                Ableton Push 3, Native Instruments Komplete Kontrol S61 Mk3, аналоговые синтезаторы Korg Minilogue, Moog Subsequent 37 и Arturia; драм-машины Elektron Digitakt и Analog Rytm
                            </p>
                            <div class="oor-studio-equipment-item-image">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-5.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-5.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-5.png" alt="Инструменты и контроллеры" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                    </div>
                    
                    <div
                        class="oor-studio-equipment-row"
                        data-image-png="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-6.png"
                        data-image-webp="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-6.webp"
                        data-image-avif="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-6.avif"
                    >
                        <div class="oor-studio-equipment-index">06</div>
                        <div class="oor-studio-equipment-content">
                            <div class="oor-studio-equipment-header-row">
                                <h3 class="oor-studio-equipment-item-title">DAW И ПЛАГИНЫ</h3>
                                <div class="oor-studio-equipment-item-icon">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="17" height="17">
                                </div>
                            </div>
                            <p class="oor-studio-equipment-item-description">
                                Pro Tools, Logic Pro X, Ableton Live; плагины Waves, FabFilter, Soundtoys, iZotope, Melodyne, Auto-Tune Pro
                            </p>
                            <div class="oor-studio-equipment-item-image">
                                <picture>
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-6.avif" type="image/avif">
                                    <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-6.webp" type="image/webp">
                                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-6.png" alt="DAW и плагины" class="oor-media-cover">
                                </picture>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="oor-studio-equipment-image">
                    <picture>
                        <source data-equipment-image-avif srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.avif" type="image/avif">
                        <source data-equipment-image-webp srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.webp" type="image/webp">
                        <img id="equipment-image" src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-equipment-image-1.png" alt="Оборудование студии" class="oor-media-cover">
                    </picture>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="oor-studio-services-section">
        <div class="oor-container">
            <div class="oor-studio-services-header">
                <h2 class="oor-studio-services-title">ЧТО МЫ ДЕЛАЕМ:</h2>
                <div class="oor-studio-services-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-large.svg" alt="" width="25" height="18">
                </div>
            </div>
            
            <div class="oor-studio-services-grid">
                <div class="oor-studio-service-column">
                    <div class="oor-studio-service-item">
                        <div class="oor-studio-service-index">01</div>
                        <div class="oor-studio-service-content">
                            <h3 class="oor-studio-service-title">ПРОДАКШН</h3>
                        </div>
                    </div>
                    <div class="oor-studio-service-description">
                        <div class="oor-studio-service-description-header">
                            <div class="oor-studio-service-description-index">01</div>
                            <div class="oor-studio-service-description-title-wrapper">
                                <h3 class="oor-studio-service-description-title">ПРОДАКШН</h3>
                            </div>
                        </div>
                        <p>Работаем со звуком, видео и фото — от первоначальной идеи до полноценной записи/съемки</p>
                    </div>
                </div>
                <div class="oor-studio-service-column">
                    <div class="oor-studio-service-item">
                        <div class="oor-studio-service-index">02</div>
                        <div class="oor-studio-service-content">
                            <h3 class="oor-studio-service-title">СВЕДЕНИЕ И МАСТЕРИНГ</h3>
                        </div>
                    </div>
                    <div class="oor-studio-service-description">
                        <div class="oor-studio-service-description-header">
                            <div class="oor-studio-service-description-index">02</div>
                            <div class="oor-studio-service-description-title-wrapper">
                                <h3 class="oor-studio-service-description-title">СВЕДЕНИЕ И МАСТЕРИНГ</h3>
                            </div>
                        </div>
                        <p>Хип-хоп, оркестровая музыка, синематик, трип-хоп, нью-вейв, синти-поп, рок, фолк, джаз, r'n'b, техно — собираем сетап под любой жанр. Мы не навязываем правки, если они не принципиальны — финальный трек остается с вашим характером и звучит четко где угодно</p>
                    </div>
                </div>
                <div class="oor-studio-service-column">
                    <div class="oor-studio-service-item">
                        <div class="oor-studio-service-index">03</div>
                        <div class="oor-studio-service-content">
                            <h3 class="oor-studio-service-title">ТЮНИНГ ВОКАЛА</h3>
                        </div>
                    </div>
                    <div class="oor-studio-service-description">
                        <div class="oor-studio-service-description-header">
                            <div class="oor-studio-service-description-index">03</div>
                            <div class="oor-studio-service-description-title-wrapper">
                                <h3 class="oor-studio-service-description-title">ТЮНИНГ ВОКАЛА</h3>
                            </div>
                        </div>
                        <p>Исправляем ошибки вокалиста, подтягиваем ноты и можем добавить к голосу спецэффекты</p>
                    </div>
                </div>
                <div class="oor-studio-service-column">
                    <div class="oor-studio-service-item">
                        <div class="oor-studio-service-index">04</div>
                        <div class="oor-studio-service-content">
                            <h3 class="oor-studio-service-title">АРАНЖИРОВКА</h3>
                        </div>
                    </div>
                    <div class="oor-studio-service-description">
                        <div class="oor-studio-service-description-header">
                            <div class="oor-studio-service-description-index">04</div>
                            <div class="oor-studio-service-description-title-wrapper">
                                <h3 class="oor-studio-service-description-title">АРАНЖИРОВКА</h3>
                    </div>
                    </div>
                        <p>Собираем структуру, развиваем динамику и балансируем инструменты по их ролям в общей композиции</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recording Section [04] -->
    <section class="oor-studio-recording-section">
        <div class="oor-container">
            <div class="oor-studio-recording-header">
                <h2 class="oor-studio-recording-title">СТУДИЯ ПОДХОДИТ ДЛЯ ЗАПИСИ:</h2>
                <div class="oor-studio-section-index">[04]</div>
            </div>
            <div class="oor-studio-recording-divider"></div>
            
            <div class="oor-grid oor-studio-recording-grid">
                <div class="oor-col-7 oor-studio-recording-image">
                    <picture>
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-recording-image.avif" type="image/avif">
                        <source srcset="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-recording-image.webp" type="image/webp">
                        <img src="<?php echo get_template_directory_uri(); ?>/public/assets/oor-studio-recording-image.png" alt="Запись в студии" class="oor-media-cover">
                    </picture>
                </div>
                
                <div class="oor-col-5 oor-studio-recording-list">
                    <div class="oor-studio-recording-items">
                        <div class="oor-studio-recording-item">
                            <h3 class="oor-studio-recording-item-title">АВТОРСКОЙ МУЗЫКИ</h3>
                            <div class="oor-studio-recording-item-divider"></div>
                        </div>
                        <div class="oor-studio-recording-item">
                            <h3 class="oor-studio-recording-item-title">ПЕСЕН, ХИП-ХОПА И РЭПА</h3>
                            <div class="oor-studio-recording-item-divider"></div>
                        </div>
                        <div class="oor-studio-recording-item">
                            <h3 class="oor-studio-recording-item-title">ПОДКАСТОВ, ЛЕКЦИЙ И АУДИОКНИГ</h3>
                            <div class="oor-studio-recording-item-divider"></div>
                        </div>
                        <div class="oor-studio-recording-item">
                            <h3 class="oor-studio-recording-item-title">САУНДТРЕКОВ К КИНО, ИГРАМ И ШОУ</h3>
                            <div class="oor-studio-recording-item-divider"></div>
                        </div>
                        <div class="oor-studio-recording-item">
                            <h3 class="oor-studio-recording-item-title">РЕКЛАМНОЙ МУЗЫКИ И ДЖИНГЛОВ</h3>
                            <div class="oor-studio-recording-item-divider"></div>
                        </div>
                    </div>
                    
                    <div class="oor-studio-contact">
                        <p class="oor-studio-contact-text">
                            Хотите арендовать студию? Есть вопросы по техническому оснащению? Нужен профессиональный совет по треку?
                        </p>
                        <a href="<?php echo esc_url( home_url( '/contacts' ) ); ?>" class="oor-studio-contact-button rolling-button">
                            <span class="tn-atom">Напишите нам</span>
                            <img src="<?php echo get_template_directory_uri(); ?>/public/assets/plus-small.svg" alt="" width="32" height="32">
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="oor-studio-recording-divider"></div>
        </div>
    </section>

<?php
get_footer();
?>
