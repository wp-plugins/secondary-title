<?php
	/**
	 * Plugin Name:  Secondary Title
	 * Plugin URI:   http://www.koljanolte.com/wordpress/plugins/secondary-title/
	 * Description:  Adds a secondary title to posts, pages and custom post types.
	 * Version:      1.2
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
		include($include_file);
	}

	register_activation_hook(__FILE__, "secondary_title_install");

	/** Loads the text domain for localization. */
	load_plugin_textdomain("secondary_title", false, dirname(plugin_basename(__FILE__)) . "/../languages/");