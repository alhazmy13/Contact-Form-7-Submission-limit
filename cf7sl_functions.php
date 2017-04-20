<?php
/*
Plugin Name: Contact Form 7 Submission limit
Version: 1.0
Plugin URI: https://github.com/alhazmy13/Contact-Form-7-Submission-limit
Description:
Author: Abdullah Alhazmy
Author URI: http://alhazmy13.net/
License: GPLv2 or later
Text Domain: contact-form-7-submission-limit
 */

/*
Copyright 2017 Abdullah Alhazmy

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
if (!defined('ABSPATH')) {
    exit;
}

function cf7sl_plugin_meta($links, $file)
{
    // add some links to plugin meta row
    if (strpos($file, 'contact-form-7-submission-limit.php') !== false) {
        $links = array_merge($links, array('<a href="' . esc_url(get_admin_url(null, 'tools.php?page=contact-form-7-submission-limit')) . '">Settings</a>'));
    }
    return $links;
}

/*
 * Add a submenu under Tools
 */
function cf7sl_add_pages()
{
    $page = add_submenu_page('tools.php', 'Contact Form 7 Submission limit', 'Contact Form 7 Submission limit', 'activate_plugins', 'contact-form-7-submission-limit', 'cf7sl_options_page');
    add_action("admin_print_scripts-$page", "cf7sl_admin_scripts");
}

/*
 * Scripts needed for the admin side
 */
function cf7sl_admin_scripts()
{
    wp_enqueue_script('cf7sl_dynamicfields', plugins_url() . '/contact-form-7-submission-limit/js/jquery.dynamicfields.js', array('jquery'));
    wp_enqueue_script('jquery-ui-1', plugins_url() . '/contact-form-7-submission-limit/js/jquery-ui-1.10.3.custom.min.js', array('jquery'));
    wp_enqueue_script('cf7sl_functions', plugins_url() . '/contact-form-7-submission-limit/js/cf7sl.functions.js', array('jquery'));
    wp_enqueue_style('cf7sl_styles', plugins_url() . '/contact-form-7-submission-limit/css/cf7sl.css');
}

/*
 * Apply find and replace rules
 */
function cf7sl_ob_call($buffer)
{
    // $buffer contains entire page
    $cf7sl_settings = get_option('cf7sl_plugin_settings');
    if (is_array($cf7sl_settings['cf7slfind'])) {
        foreach ($cf7sl_settings['cf7slfind'] as $key => $find) {
            if (is_form_in_list($buffer, $find) && is_submition_above_limit($find, $cf7sl_settings['limit'][$key])) {
                $buffer = preg_replace("/<form\b[^>]*[class=\"wpcf7\-form\"]\b[^>]*>(.*?)<\/form>/is", $cf7sl_settings['cf7slreplace'][$key], $buffer);
            }
        }
    }
    return $buffer;
}

function is_form_in_list($buffer, $form_id)
{
    $pattern = '/<input(.*?)name=\"_wpcf7\"(.*)value=\"(.*?)\"/i';
    preg_match_all($pattern, $buffer, $matches);
    return $matches[3][0] == $form_id;
}

function is_submition_above_limit($form_id, $limit)
{
    global $wpdb;
    $form_name = $wpdb->get_results('SELECT `post_title`  FROM ' . $wpdb->prefix . 'posts WHERE ID = ' . $form_id);
    if ($form_name) {
        $form_name = $form_name[0]->post_title;
    } else {
        return false;
    }
    $result = $wpdb->get_results('SELECT count(*) as "count" FROM ' . $wpdb->prefix . 'cf7dbplugin_submits WHERE form_name = \'' . $form_name . '\'');
    if ($result && $limit <= $result[0]->count) {
        return true;
    }
    return false;
}

function get_total_submitions()
{
    global $wpdb;
    $total_submitions = $wpdb->get_results('SELECT `wp_posts`.`ID`,form_name,count(`form_name`) as count FROM ' . $wpdb->prefix . 'cf7dbplugin_submits join ' . $wpdb->prefix . 'posts where `wp_cf7dbplugin_submits`.`form_name` = `wp_posts`.`post_name` group by `form_name`');
    if ($total_submitions) {
        return $total_submitions;
    } else {
        return null;
    }
}
function get_form_list()
{
    global $wpdb;
    $formList = $wpdb->get_results('SELECT `post_title`,ID  FROM ' . $wpdb->prefix . 'posts WHERE post_type = \'wpcf7_contact_form\'');
    if ($formList) {
        return $formList;
    } else {
        return null;
    }
}
function cf7sl_template_redirect()
{
    ob_start();
    ob_start('cf7sl_ob_call');
}

//Add left menu item in admin
add_action('admin_menu', 'cf7sl_add_pages');

//Add additional links below plugin description on plugin page
add_filter('plugin_row_meta', 'cf7sl_plugin_meta', 10, 2);

//Handles find and replace for public pages
add_action('template_redirect', 'cf7sl_template_redirect');