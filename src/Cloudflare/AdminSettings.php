<?php

namespace Cloudflare;

class AdminSettings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function addSettingsPage(): void
    {
        add_options_page(
            'Cloudflare Purger Settings',
            'Cloudflare Purger',
            'manage_options',
            'cf-purger',
            [$this, 'renderSettingsPage']
        );
    }

    public function registerSettings(): void
    {
        register_setting('cf_purger_settings', 'cf_purger_zone_id');
        register_setting('cf_purger_settings', 'cf_purger_api_token');
        register_setting('cf_purger_settings', 'cf_purger_mode');

        add_settings_section('cf_purger_main', 'Settings', null, 'cf-purger');

        add_settings_field('cf_purger_zone_id', 'Cloudflare Zone ID', function () {
            printf('<input type="text" name="cf_purger_zone_id" value="%s" class="regular-text" />',
                esc_attr(get_option('cf_purger_zone_id', '')));
        }, 'cf-purger', 'cf_purger_main');

        add_settings_field('cf_purger_api_token', 'Cloudflare API Token', function () {
            printf('<input type="password" name="cf_purger_api_token" value="%s" class="regular-text" />',
                esc_attr(get_option('cf_purger_api_token', '')));
        }, 'cf-purger', 'cf_purger_main');

        add_settings_field('cf_purger_mode', 'Purge Mode', function () {
            $mode = get_option('cf_purger_mode', 'everything');
            echo '<select name="cf_purger_mode">';
            echo '<option value="everything" ' . selected($mode, 'everything', false) . '>Purge Everything</option>';
            echo '<option value="post" ' . selected($mode, 'post', false) . '>Only Purge Updated Post URL</option>';
            echo '</select>';
        }, 'cf-purger', 'cf_purger_main');
    }

    public function renderSettingsPage(): void
    {
        echo '<div class="wrap"><h1>Cloudflare Purger Settings</h1><form method="post" action="options.php">';
        settings_fields('cf_purger_settings');
        do_settings_sections('cf-purger');
        submit_button();
        echo '</form></div>';
    }
}
