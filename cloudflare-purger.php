<?php
/**
 * Plugin Name: Cloudflare Cache Purger
 * Description: Purges Cloudflare cache on post publish/update.
 * Author: Ash
 */

require_once __DIR__ . '/vendor/autoload.php';

use Cloudflare\PurgeService;
use Cloudflare\AdminSettings;

add_action('plugins_loaded', function () {
    new AdminSettings();
});

add_action('save_post', function ($postId) {
    if (wp_is_post_autosave($postId) || wp_is_post_revision($postId)) {
        return;
    }

    $zoneId = get_option('cf_purger_zone_id');
    $apiToken = get_option('cf_purger_api_token');
    $purgeMode = get_option('cf_purger_mode', 'everything');

    if (!$zoneId || !$apiToken) {
        error_log('Cloudflare Purger: Missing zone ID or API token.');
        return;
    }

    $purger = new PurgeService($zoneId, $apiToken);

    if ($purgeMode === 'post') {
        $url = get_permalink($postId);
        $purger->purgeUrls([$url]);
    } else {
        $purger->purgeEverything();
    }
});
