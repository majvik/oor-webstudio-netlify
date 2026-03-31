<?php
/**
 * Удаляет из БД группу полей ACF с ключом group_contacts_page.
 *
 * После удаления синхронизируйте актуальную группу из JSON в ACF (например group_oor_contacts_page),
 * если она ещё не импортирована.
 *
 * Запуск в Docker:
 *   docker compose exec wordpress php wp-content/themes/oor-theme/scripts/delete-acf-group-contacts-page-from-db.php
 */

if (php_sapi_name() !== 'cli') {
    exit('CLI only.');
}

$wp_load = dirname(__DIR__, 4) . '/wp-load.php';
if (!is_readable($wp_load)) {
    fwrite(STDERR, "wp-load.php not found: {$wp_load}\n");
    exit(1);
}

require $wp_load;

if (!function_exists('acf_delete_field_group') || !function_exists('acf_get_field_group')) {
    fwrite(STDERR, "ACF is not active.\n");
    exit(1);
}

$key   = 'group_contacts_page';
$group = acf_get_field_group($key);

if (!$group) {
    echo "Группа {$key} не найдена (уже удалена или другой ключ в БД).\n";
    exit(0);
}

$result = acf_delete_field_group($key);

echo $result
    ? "Удалена группа ACF «{$key}» (ID записи {$group['ID']}).\n"
    : "Не удалось удалить «{$key}».\n";

exit($result ? 0 : 1);
