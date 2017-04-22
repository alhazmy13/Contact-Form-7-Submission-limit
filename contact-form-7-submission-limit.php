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

function cf7sl_options_page()
{
    if (isset($_POST['setup-update'])) {
        $_POST = stripslashes_deep($_POST);

        // If atleast one find has been submitted
        if (isset($_POST['cf7slfind']) && is_array($_POST['cf7slfind'])) {
            foreach ($_POST['cf7slfind'] as $key => $find) {

                // If empty ones have been submitted we get rid of the extra data submitted if any.
                if (empty($find)) {
                    unset($_POST['cf7slfind'][$key]);
                    unset($_POST['cf7sllimit'][$key]);
                    unset($_POST['cf7slreplace'][$key]);
                }

                // Convert line feeds on non-regex only
                if (!isset($_POST['cf7slregex'][$key])) {
                    $_POST['cf7slfind'][$key] = str_replace("\r\n", "\n", $find);
                }
            }
        }
        unset($_POST['setup-update']);
        unset($_POST['import-text']);
        unset($_POST['export-text']);
        unset($_POST['submit-import']);

        // Delete the option if there are no settings. Keeps the database clean if they aren't using it and uninstalled.
        if (empty($_POST['cf7slfind'])) {
            delete_option('cf7sl_plugin_settings');
        } else {
            update_option('cf7sl_plugin_settings', $_POST);
        }
        echo '<div id="message" class="updated fade">';
        echo '<p><strong>Options Updated</strong></p>';
        echo '</div>';
    }
    ?>
<div class="wrap" style="padding-bottom:5em;">
    <h2>Contact-Form7 Submmition Limit</h2>
    <p>Click "Add" to begin. Then enter your rules cases below. Click and drag to change the order. </p>
    <div id="cf7sl-items">

        <form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]); ?>">
            <?php echo '<textarea hidden id="form_list" name="form_list">' . json_encode(get_form_list()) . '</textarea>'; ?>
            <?php echo '<textarea hidden id="total_submitions" name="total_submitions">' . json_encode(get_total_submitions()) . '</textarea>'; ?>
            <input type="button" class="button left" value="Add" onClick="addFormField(); return false;" />
            <input type="submit" class="button left" value="Update Settings" name="update" id="update" />
            <input type="hidden" name="setup-update" />
            <br style="clear: both;" />
            <?php $cf7sl_settings = get_option('cf7sl_plugin_settings');?>
            <ul id="cf7sl_itemlist">
            <?php
$i = 0;
    // If there are any finds already set
    if (isset($cf7sl_settings['cf7slfind']) && is_array($cf7sl_settings['cf7slfind'])) {
        $i = 1;
        foreach ($cf7sl_settings['cf7slfind'] as $key => $find) {

            if (isset($cf7sl_settings['cf7slreplace'][$key])) {
                $cf7sl_replace = $cf7sl_settings['cf7slreplace'][$key];
            } else {
                $cf7sl_replace = '';
            }

            if (isset($cf7sl_settings['cf7sllimit'][$key])) {
                $cf7sl_limit = $cf7sl_settings['cf7sllimit'][$key];
            } else {
                $cf7sl_limit = '-1';
            }

            $current_total_submition = get_total_submition($cf7sl_settings['cf7slfind'][$key]);
            $status = "Remaining submissions = " . ($cf7sl_limit - $current_total_submition) . "</br>Form Status = " . get_form_status($cf7sl_limit - $current_total_submition);

            echo "<li id='row$i'>";

            echo "<div style='float: left'>";
            echo "<div style='float: left'>";
            echo "<label for='cf7slfind$i'>Form ID:</label>";
            echo "<br />";
            echo "<select name='cf7slfind[$i]' id='cf7slfind$i' onchange='getFormSelectValue(this);'>";
            $forms_list = get_form_list();
            if ($forms_list) {
                foreach ($forms_list as $key => $value) {
                    if ($find == $value->ID) {
                        echo "<option  name='cf7slfind[$i]' id='cf7slfind$i' value='" . $value->ID . "' selected>" . $value->post_title . "</option>";
                    } else {
                        echo "<option  name='cf7slfind[$i]' id='cf7slfind$i' value='" . $value->ID . "'>" . $value->post_title . "</option>";
                    }
                }
            }
            echo "</select>";
            echo "</div>";
            echo "<br />";

            echo "<div style='float: left'>";
            echo "<label for='cf7sllimit$i'>Limit:</label>";
            echo "<br />";
            echo "<input class='textbox' min='-1' type='number' name='cf7sllimit[$i]' onchange='updateStatusValue(this);' id='cf7sllimit$i' value='" . $cf7sl_limit . "'></input>";
            echo "</div>";
            echo "<br />";
            echo "</div>";

            echo "<div style='float: right'>";
            echo "<div style='float: left'>";
            echo "<label for='submitions$i'>Current total submitions:</label>";
            echo "<br />";
            echo "<input class='textbox' disabled type='text' name='submitions[$i]' id='submitions$i' value='" . $current_total_submition . "'></input>";
            echo "</div>";
            echo "<br />";

            echo "<div style='float: left'>";
            echo "<div id='status$i' name='status$i' style='display:inline'>" . $status . "</div>";
            echo "</div>";
            echo "<br />";
            echo "</div>";

            echo "<div style='clear: both;'>";
            echo "<div >";
            echo "<label for='cf7slreplace$i'>Replace With:</label>";
            echo "<br />";
            echo "<textarea style=' width: 100%'class='left' name='cf7slreplace[$i]' id='cf7slreplace$i'>" . esc_textarea($cf7sl_replace) . "</textarea>";
            echo "</div>";
            echo "<br />";
            echo "</div>";

            echo "<div>";
            echo "<input style='margin-right: 9px' type='button' class='button right remove' value='Remove' onClick='removeFormField(\"#row$i\"); return false;' />";
            echo "</div>";

            echo "</li>";
            unset($regex_checked);
            $i = $i + 1;
        }
    } else {
        // Do nothing
    }
    ?>
            </ul>
            <div id="divTxt"></div>
            <div class="clearpad"></div>
            <input type="button" class="button left" value="Add" onClick="addFormField(); return false;" />
            <input type="submit" class="button left" value="Update Settings" />
            <input type="hidden" id="id" value="<?php echo $i; /* used so javascript returns unique ids */ ?>" />
        </form>
    </div>
    <div id="cf7sl-sb">
        <div class="postbox" id="cf7sl-sbone">
            <h3 class="hndle"><span>Documentation</span></h3>
            <div class="inside">
                <strong>Instructions</strong>
                <p>This plugin will replace `contact form 7` with any HTML code when the number of submissions goes above your limit.</p>
                <ol>
                <li>Select any form from a drop-down list.</li>
                <li>Enter your limit (if the number are above or equal the total number of submissions then the for will be hidden).</li>
                <li>Enter any HTML code that you want to replace the form with it.</li>
                <li>Click on update setting button.</li>
                </ol>
                <strong>Tips</strong>
                <ol>
                <li>The status of your form will automatically show after you select the number of limits.</li>
                <li>If you want unlimited submission then you can remove the rule or just type -1 in the limit field.</li>
                <li>Not seeing your changes? Turn off your cache!</li>
                </ol>
            </div>
        </div>
        <div class="postbox" id="cf7sl-sbtwo">
            <h3 class="hndle"><span>Support</span></h3>
            <div class="inside">
                <p>Your best bet is to post on the <a href="https://github.com/alhazmy13/Contact-Form-7-Submission-limit">plugin support page</a>.</p>
                <p>Please consider supporting me by <a href="https://github.com/alhazmy13/Contact-Form-7-Submission-limit">rating this plugin</a>. Thanks!</p>
            </div>
        </div>
    </div>
<script type="text/javascript">

// Function to update total submitions after dropdown list changed
function getFormSelectValue(sel) {
    var id = getInputID(sel.name);
    if (id != -1) {
        jQuery('#submitions' + id).val(getTotalSubmition(sel.value));
    }
}

// Function to update form status after limit number changed
function updateStatusValue(input) {
    var id = getInputID(input.name);
    if (id != -1) {
        var current_total_submition = jQuery('#submitions' + id).val();
        var cf7sl_limit = jQuery('#cf7sllimit' + id).val();
        var remining = input.value - current_total_submition;
        if(cf7sl_limit == -1){
            remining = "∞";
        }
        var form_status;
        if (cf7sl_limit == -1 || (cf7sl_limit - current_total_submition > 0)){
             form_status =  '<p style="color:green;display:inline;">Open</p>';
        }else{
           form_status =  '<p style="color:red;display:inline;">Closed</p>';
        }
        var status = jQuery('#status' + id).html("Remaining submissions = " + remining + " <br/> Form Status = " + form_status);
    }
}

// Function to get id from input name
function getInputID(name) {
    var matches = name.match(/\[(.*?)\]/);
    if (matches) {
        var submatch = matches[1];
        return submatch;
    }
    return -1;
}

// Function to get total number of submition for x form
function getTotalSubmition(formID) {
    var count = 0;
    var total_submitions = jQuery.parseJSON(jQuery('#total_submitions').val());
    total_submitions.forEach(function(item) {
        if (item['ID'] == formID) {
            count =  item['count'];
            return;
        }
    });
    return count;
}

</script>

</div>
<?php }

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
            error_log($cf7sl_settings['cf7sllimit'][$key]);
            if (is_form_in_list($buffer, $find) && is_submition_above_limit($find, $cf7sl_settings['cf7sllimit'][$key])) {
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
    error_log($matches[3][0]);
    return $matches[3][0] == $form_id;
}

function is_submition_above_limit($form_id, $limit)
{
    if ($limit == -1) {
        return false;
    }
    global $wpdb;
    $form_name = $wpdb->get_results('SELECT `post_title`  FROM ' . $wpdb->prefix . 'posts WHERE ID = ' . $form_id);
    if ($form_name) {
        $form_name = $form_name[0]->post_title;
    } else {
        return false;
    }
    $result = $wpdb->get_results('SELECT form_name,count(DISTINCT `submit_time`) as "count" FROM ' . $wpdb->prefix . 'cf7dbplugin_submits WHERE form_name = \'' . $form_name . '\' ');
    if ($result && $limit <= $result[0]->count) {
        return true;
    }
    return false;
}

function get_total_submitions()
{
    global $wpdb;
    $total_submitions = $wpdb->get_results('SELECT ' . $wpdb->prefix . 'posts.`ID`,form_name,count(DISTINCT `submit_time`) as count FROM  ' . $wpdb->prefix . 'cf7dbplugin_submits join ' . $wpdb->prefix . 'posts where ' . $wpdb->prefix . 'cf7dbplugin_submits.`form_name` = ' . $wpdb->prefix . 'posts.`post_title` group by form_name');
    if ($total_submitions) {
        return $total_submitions;
    } else {
        return null;
    }
}

function get_total_submition($id)
{
    $total_array = get_total_submitions();
    foreach ($total_array as $value) {
        if ($value->ID === $id) {
            return $value->count;
        }
    }
    return 0;
}

function get_form_status($total)
{
    if ($total <= 0) {
        return '<p style="color:red;display:inline;">Closed</p>';
    }
    return '<p style="color:green;display:inline;">Open</p>';
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
