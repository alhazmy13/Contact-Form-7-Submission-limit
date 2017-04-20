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


require "cf7sl_functions.php";


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
	<p>Click "Add" to begin. Then enter your find and replace cases below. Click and drag to change the order. </p>
	<div id="cf7sl-items">

		<form method="post" action="<?php echo esc_url($_SERVER["REQUEST_URI"]); ?>">
			<?php echo '<textarea hidden id="test" name="test">' . json_encode(get_form_list()) . '</textarea>'; ?>
			<?php echo '<textarea id="total_submitions" name="total_submitions">' . json_encode(get_total_submitions()) . '</textarea>'; ?>
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
                $limit = $cf7sl_settings['cf7sllimit'][$key];
            } else {
                $limit = '';
            }

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
		            echo "<label for='limit$i'>Limit:</label>";
		            echo "<br />";
		            echo "<input class='textbox' type='number' name='cf7sllimit[$i]' onchange='updateStatusValue(this);' id='cf7sllimit$i' value='" . $limit . "'></input>";
		            echo "</div>";
		            echo "<br />";
	            echo "</div>";

            echo "<div style='float: right'>";
	            echo "<div style='float: left'>";
	            echo "<label for='submitions$i'>Current total submitions:</label>";
	            echo "<br />";
	            echo "<input class='textbox' disabled type='text' name='submitions[$i]' id='submitions$i' value='0'></input>";
            echo "</div>";
            echo "<br />";

            echo "<div style='float: left'>";
	            echo "<p id='status$i' name='status$i'></p>";
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
	
</script>

</div>
<?php }?>
<?php
