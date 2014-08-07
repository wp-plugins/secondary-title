<?php
	/**
	 * Plugin Name:  Secondary Title
	 * Plugin URI:   http://www.koljanolte.com/wordpress/plugins/secondary-title/
	 * Description:  Adds a secondary title to posts, pages and custom post types.
	 * Version:      0.9.1
	 * Author:       Kolja Nolte
	 * Author URI:   http://www.koljanolte.com
	 * License:      GPLv2 or later
	 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
	 */

	/**
	 * Stop script when the file is called directly.
	 */
	if(!function_exists("add_action")) {
		return false;
	}

	/**
	 * Includes all files from the "includes" directory.
	 */
	$include_files = glob(dirname(__FILE__) . "/includes/*.php");
	foreach($include_files as $include_file) {
		/** @noinspection PhpIncludeInspection */
		include($include_file);
	}

	/**
	 * Sets the default settings when plugin is activated.
	 */
	function init_secondary_title_default_settings() {
		/** Use update_option() to create the default options  */
		foreach(get_secondary_title_default_settings() as $setting => $value) {
			if(get_option($setting) == "") {
				update_option($setting, $value);
			}
			else {
				return false;
			}
		}
		return true;
	}

	register_activation_hook(__FILE__, "init_secondary_title_default_settings");

	/*
	 * As of version 0.7, Secondary Title has been completely restructured
	 * to make it easier to understand the way the plugin works. Please see
	 * the directory "includes" for the rest of the plugin files.
	 */