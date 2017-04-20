# Contact form 7 submission limit 
* Contributors: `Abdullah Alhazmy`
* site: `http://alhazmy13.net`
* Tags: `contact, form, contact form 7, Submission, limit` 
* Requires at least: `4.0`
* Tested up to: `4.7.3`
* License: `GPLv2 or later`

# Download 
last version you can found it in this ![link](https://github.com/alhazmy13/Contact-Form-7-Submission-limit/releases)
# Installation

1- Upload the contact-form-7-submission-limit folder to the `/wp-content/plugins/` directory
2- Activate the plugin through the 'Plugins' menu in WordPress
3- The contact-form-7-submission-limit are in the Tools sidebar menu.
4- Click on the Add on the admin page to add new rule.

# Note 
This plugin requires two plugins ![contact-form-7](https://wordpress.org/plugins/contact-form-7/) and ![CFDB](https://github.com/mdsimpson/contact-form-7-to-database-extension/releases) please make sure that you have it on your site.

# Description 
This plugin will replace `contact form 7` with any HTML code when the number of submissions goes above your limit.

*  Select any form from a drop-down list.
* Enter your limit (if the number are above or equal the total number of submissions then the for will be hidden).
* Enter any HTML code that you want to replace the form with it.
* Click on update setting button.


# Tips

* The status of your form will automatically show after you select the number of limits.
* If you want unlimited submission then you can remove the rule or just type -1 in the limit field.
* Not seeing your changes? Turn off your cache!


# Where is data stored?

In an array in the `wp_options` table. Just one record regardless of the number of your rules.


# Changelog

= 1.0 =
* Initial release.
