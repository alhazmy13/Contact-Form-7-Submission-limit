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

//Exit if accessed directly


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function far_plugin_meta( $links, $file ) { // add some links to plugin meta row
	if ( strpos( $file, 'contact-form-7-submission-limit.php' ) !== false ) {
		$links = array_merge( $links, array( '<a href="' . esc_url( get_admin_url(null, 'tools.php?page=contact-form-7-submission-limit') ) . '">Settings</a>' ) );
		// $links = array_merge( $links, array( '<a href="http://infolific.com/technology/software-worth-using/real-time-find-and-replace-for-wordpress/#pro-version" target="_blank">Pro Version</a> (less than $10)' ) );
	}
	return $links;
}

/*
* Add a submenu under Tools
*/
function far_add_pages() {
	$page = add_submenu_page( 'tools.php', 'Contact Form 7 Submission limit', 'Contact Form 7 Submission limit', 'activate_plugins', 'contact-form-7-submission-limit', 'far_options_page' );
	add_action( "admin_print_scripts-$page", "far_admin_scripts" );
}

function far_options_page() {
	if ( isset( $_POST['setup-update'] ) ) {
		$_POST = stripslashes_deep( $_POST );
		
		// If atleast one find has been submitted
		if ( isset ( $_POST['farfind'] ) && is_array( $_POST['farfind'] ) ) { 
			foreach ( $_POST['farfind'] as $key => $find ){

				// If empty ones have been submitted we get rid of the extra data submitted if any.
				if ( empty($find) ){ 
					unset( $_POST['farfind'][$key] );
					unset( $_POST['limit'][$key] );
					unset( $_POST['farreplace'][$key] );
				}
				
				// Convert line feeds on non-regex only
				if ( !isset( $_POST['farregex'][$key] ) ) {
					$_POST['farfind'][$key] = str_replace( "\r\n", "\n", $find );
				}
			}
		}
		unset( $_POST['setup-update'] );
		unset( $_POST['import-text'] );
		unset( $_POST['export-text'] );
		unset( $_POST['submit-import'] );
		
		// Delete the option if there are no settings. Keeps the database clean if they aren't using it and uninstalled.
		if( empty( $_POST['farfind'] ) ) {
			delete_option( 'far_plugin_settings' );
		} else {
			update_option( 'far_plugin_settings', $_POST );
		}
		echo '<div id="message" class="updated fade">';
			echo '<p><strong>Options Updated</strong></p>';
		echo '</div>';
	}
?>
<div class="wrap" style="padding-bottom:5em;">
	<h2>Contact-Form7 Submmition Limit</h2>
	<p>Click "Add" to begin. Then enter your find and replace cases below. Click and drag to change the order. </p>
	<div id="far-items">

		<form method="post" action="<?php echo esc_url( $_SERVER["REQUEST_URI"] ); ?>">
			<?php echo '<textarea hidden id="test" name="test">'. json_encode(get_form_list()) .'</textarea>';?>
			<input type="button" class="button left" value="Add" onClick="addFormField(); return false;" />
			<input type="submit" class="button left" value="Update Settings" name="update" id="update" />
			<input type="hidden" name="setup-update" />
			<br style="clear: both;" />
			<?php $far_settings = get_option( 'far_plugin_settings' ); ?>
			<ul id="far_itemlist">
			<?php
				$i = 0;
				// If there are any finds already set
				if ( isset ( $far_settings['farfind'] ) && is_array( $far_settings['farfind'] ) ){
					$i = 1;
					foreach ( $far_settings['farfind'] as $key => $find ){

						if ( isset( $far_settings['farreplace'][$key] ) ) {
							$far_replace = $far_settings['farreplace'][$key];
						} else {
							$far_replace = '';
						}

						if ( isset( $far_settings['limit'][$key] ) ) {
							$limit = $far_settings['limit'][$key];
						} else {
							$limit = '';
						}


						echo "<li id='row$i'>";

						echo "<div style='float: left'>";
							echo "<div style='float: left'>";
							echo "<label for='farfind$i'>Form ID:</label>";
							echo "<br />";
							echo "<select name='farfind[$i]' id='farfind$i'>";
							$forms_list = get_form_list();
							if($forms_list){
								foreach ($forms_list as $key => $value) {
									if($find == $value->ID){
										echo "<option  name='farfind[$i]' id='farfind$i' value='". $value->ID ."' selected>" . $value->post_title  . "</option>";
									}else{
										echo "<option  name='farfind[$i]' id='farfind$i' value='". $value->ID ."'>" . $value->post_title  . "</option>";
									}
								}
							}
							echo "</select>";
							echo "</div>";
							echo "<br />";

							echo "<div style='float: left'>";
							echo "<label for='limit$i'>Limit:</label>";
							echo "<br />";
							echo "<input class='textbox' type='number' name='limit[$i]' id='limit$i' value='".$limit."'></input>";
							echo "</div>";
							echo "<br />";

							echo "<div style='float: left'>";
							echo "<label for='farreplace$i'>Replace With:</label>";
							echo "<br />";
							echo "<textarea class='left' name='farreplace[$i]' id='farreplace$i'>" . esc_textarea( $far_replace ) . "</textarea>";
							echo "</div>";

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

	
</div>
<?php } ?>
<?php
/*
* Scripts needed for the admin side
*/
function far_admin_scripts() {
	wp_enqueue_script( 'far_dynamicfields', plugins_url() . '/contact-form-7-submission-limit/js/jquery.dynamicfields.js', array('jquery') );
	wp_enqueue_script( 'jquery-ui-1', plugins_url() . '/contact-form-7-submission-limit/js/jquery-ui-1.10.3.custom.min.js', array('jquery') );
	wp_enqueue_style( 'far_styles', plugins_url() . '/contact-form-7-submission-limit/css/far.css' );
}

/*
* Apply find and replace rules
*/
function far_ob_call( $buffer ) { // $buffer contains entire page
	$far_settings = get_option( 'far_plugin_settings' );
	if ( is_array( $far_settings['farfind'] ) ) {
		foreach ( $far_settings['farfind'] as $key => $find ) {			
			if(is_form_in_list($buffer,$find) && is_submition_above_limit($find,$far_settings['limit'][$key])){
				$buffer = 	preg_replace("/<form\b[^>]*[class=\"wpcf7\-form\"]\b[^>]*>(.*?)<\/form>/is", $far_settings['farreplace'][$key], $buffer);
			}
		}
	}
	return $buffer;
}

function is_form_in_list($buffer,$form_id){
	$pattern = '/<input(.*?)name=\"_wpcf7\"(.*)value=\"(.*?)\"/i';
	preg_match_all($pattern, $buffer, $matches);
	return $matches[3][0] == $form_id;
}

function is_submition_above_limit($form_id,$limit){
	global $wpdb;
	$form_name = $wpdb->get_results('SELECT `post_title`  FROM ' . $wpdb->prefix . 'posts WHERE ID = '. $form_id );
	if($form_name){
		$form_name = $form_name[0]->post_title;
	}else{
		return false;
	}
	$result = $wpdb->get_results('SELECT count(*) as "count" FROM ' . $wpdb->prefix . 'cf7dbplugin_submits WHERE form_name = \''.$form_name . '\'');
	if($result && $limit <= $result[0]->count ){
		return true;
	}
	return false;
}

function get_form_list(){
	global $wpdb;
	$formList = $wpdb->get_results('SELECT `post_title`,ID  FROM ' . $wpdb->prefix . 'posts WHERE post_type = \'wpcf7_contact_form\'');
	if($formList){
		return $formList;
	}else{
		return null;
	}
}
function far_template_redirect() {
	ob_start();
	ob_start( 'far_ob_call' );
}

//Add left menu item in admin
add_action( 'admin_menu', 'far_add_pages' );

//Add additional links below plugin description on plugin page
add_filter( 'plugin_row_meta', 'far_plugin_meta', 10, 2 );

//Handles find and replace for public pages
add_action( 'template_redirect', 'far_template_redirect' );