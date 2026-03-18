<?php
/**
 * Plugin Name: OOR Sync API
 * Description: Authenticated REST endpoints for prod → local file sync.
 * Version: 1.0
 *
 * Endpoints (require X-Sync-Token header matching SYNC_API_TOKEN env var):
 *   GET  /wp-json/oor-sync/v1/uploads-list   — list of all files in uploads/
 *   POST /wp-json/oor-sync/v1/uploads-tar    — tar.gz of requested files (body: JSON array of paths)
 */

add_action('rest_api_init', function () {
    $ns = 'oor-sync/v1';

    register_rest_route($ns, '/uploads-list', [
        'methods'             => 'GET',
        'callback'            => 'oor_sync_uploads_list',
        'permission_callback' => 'oor_sync_check_token',
    ]);

    register_rest_route($ns, '/uploads-tar', [
        'methods'             => 'POST',
        'callback'            => 'oor_sync_uploads_tar',
        'permission_callback' => 'oor_sync_check_token',
    ]);
});

function oor_sync_check_token() {
    $token = defined('SYNC_API_TOKEN')
        ? SYNC_API_TOKEN
        : getenv('SYNC_API_TOKEN');

    if (empty($token)) {
        return new WP_Error('no_token', 'SYNC_API_TOKEN not configured on server', ['status' => 500]);
    }

    $header = isset($_SERVER['HTTP_X_SYNC_TOKEN']) ? $_SERVER['HTTP_X_SYNC_TOKEN'] : '';
    if (!hash_equals($token, $header)) {
        return new WP_Error('forbidden', 'Invalid sync token', ['status' => 403]);
    }
    return true;
}

function oor_sync_uploads_list() {
    $dir = wp_upload_dir()['basedir'];
    $files = [];
    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($rii as $f) {
        if ($f->isFile()) {
            $rel = substr($f->getPathname(), strlen($dir) + 1);
            if (strpos($rel, 'wc-logs') === 0 || strpos($rel, 'woocommerce_uploads') === 0) continue;
            $files[] = $rel;
        }
    }
    sort($files);
    return new WP_REST_Response($files, 200);
}

function oor_sync_uploads_tar() {
    $requested = json_decode(file_get_contents('php://input'), true);
    if (!is_array($requested) || empty($requested)) {
        return new WP_Error('bad_request', 'POST body must be a JSON array of file paths', ['status' => 400]);
    }

    $dir = wp_upload_dir()['basedir'];
    $tmp = tempnam(sys_get_temp_dir(), 'sync-') . '.tar.gz';
    $list_file = tempnam(sys_get_temp_dir(), 'sync-list-');

    $valid = [];
    foreach ($requested as $rel) {
        $full = realpath($dir . '/' . $rel);
        if ($full && strpos($full, realpath($dir)) === 0 && is_file($full)) {
            $valid[] = $rel;
        }
    }

    if (empty($valid)) {
        return new WP_Error('no_files', 'No valid files to archive', ['status' => 400]);
    }

    file_put_contents($list_file, implode("\n", $valid) . "\n");
    $cmd = sprintf('cd %s && tar czf %s -T %s 2>&1',
        escapeshellarg($dir), escapeshellarg($tmp), escapeshellarg($list_file));
    shell_exec($cmd);
    unlink($list_file);

    if (!file_exists($tmp) || filesize($tmp) < 10) {
        return new WP_Error('tar_failed', 'Archive creation failed', ['status' => 500]);
    }

    header('Content-Type: application/gzip');
    header('Content-Disposition: attachment; filename="uploads-diff.tar.gz"');
    header('Content-Length: ' . filesize($tmp));
    readfile($tmp);
    unlink($tmp);
    exit;
}
