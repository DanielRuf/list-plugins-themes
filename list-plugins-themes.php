<?php

/**
 * Plugin Name: List Plugins & Themes
 * Plugin URI: https://github.com/DanielRuf/list-plugins-themes
 * Description: Display a list of all plugins and themes for support cases.
 * Version: 1.0.0
 * License: GPLv3 or later
 * Author: Daniel Ruf
 * Author URI: https://daniel-ruf.de
 */

// prevent direct access
if (!defined('ABSPATH')) {
    exit('Forbidden');
}

// prevent access outside of wp-admin
if (!function_exists('is_admin')) {
    exit('Forbidden');
}

// run the list plugin on the page
function wplpt_list_plugins_themes_init()
{
    // set some heading
    echo '<h1>List of all plugins & themes</h1>';
    // check the provided parameters
    if (
        isset($_GET['page']) &&
        $_GET['page'] === 'wplpt-list-plugins-themes' &&
        current_user_can('install_plugins') &&
        is_admin()
    ) {
        $output = "<textarea style=\"width:100%; min-height: 600px\">";

        $hostname = $_SERVER['HOSTNAME'];
        $date = date('c', time());
        $output .= "Hostname: {$hostname}\n";
        $output .= "Date & Time: {$date}\n";
        $output .= "========================================\n";
        $output .= "\n";

        $all_plugins = get_plugins();
        $output .= "Plugins:\n";
        $output .= "\n";

        foreach ($all_plugins as $plugin_file => $plugin) {

            $status = 'inactive';
            if (is_plugin_active($plugin_file)) {
                $status = 'active';
            }

            $output .= "{$plugin['Name']}\n";
            $output .= "Title: {$plugin['Title']}\n";
            $output .= "File: {$plugin_file}\n";
            $output .= "Status: {$status}\n";
            $output .= "Description: {$plugin['Description']}\n";
            $output .= "Author: {$plugin['Author']}\n";
            $output .= "AuthorURI: {$plugin['AuthorURI']}\n";
            $output .= "Version: {$plugin['Version']}\n";
            $output .= "Requires WP: {$plugin['RequiresWP']}\n";
            $output .= "Requires PHP: {$plugin['RequiresPHP']}\n";
            $output .= "========================================\n";
        }

        $output .= "\n";

        $all_themes = wp_get_themes();
        $output .= "Themes:\n";
        $output .= "\n";
        $current_theme = wp_get_theme();

        foreach ($all_themes as $theme_file => $theme) {

            $status = 'inactive';
            if (
                $theme['Name'] == $current_theme->name ||
                $theme['Name'] == $current_theme->parent_theme
            ) {
                $status = 'active';
            }

            $parent = 'no';
            $getParent = $theme->parent();
            if ($getParent) {
                $parent = $getParent['Name'];
            }

            $output .= "{$theme['Name']}\n";
            $output .= "File: {$theme_file}\n";
            $output .= "Parent theme: {$parent}\n";
            $output .= "Status: {$status}\n";
            $output .= "ThemeURI: {$theme['ThemeURI']}\n";
            $output .= "Description: {$theme['Description']}\n";
            $output .= "Author: {$theme['Author']}\n";
            $output .= "AuthorURI: {$theme['AuthorURI']}\n";
            $output .= "Version: {$theme['Version']}\n";
            $output .= "Requires PHP: {$theme['RequiresPHP']}\n";
            $output .= "Requires WP: {$theme['RequiresWP']}\n";
            $output .= "========================================\n";
        }

        $output .= "</textarea>";

        echo $output;
    } else {
        echo 'This action is not allowed.';
    }
}

// add new page to the admin menu
function wplpt_list_plugins_themes_setup_menu()
{
    // set page title, menu title, capability, menu slug and the function to call 
    add_management_page('List Plugins & Themes', 'List Plugins & Themes', 'install_plugins', 'wplpt-list-plugins-themes', 'wplpt_list_plugins_themes_init');
}

// finally call the function to add the new page
add_action('admin_menu', 'wplpt_list_plugins_themes_setup_menu');