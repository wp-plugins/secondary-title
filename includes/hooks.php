<?php
	/**
	 * This file contains the hooks used for Secondary Title.
	 * Hooks are functions that modify WordPress core functions
	 * and thus allow to change their output.
	 *
	 * @package    Secondary Title
	 * @subpackage Global
	 */

	/**
	 * Stop script when the file is called directly.
	 *
	 * @since 0.1
	 */
	if(!function_exists("add_action")) {
		return false;
	}

	/**
	 * Updates the secondary title when "Edit post" screen
	 * is being saved.
	 *
	 * @since 0.1
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	function secondary_title_edit_post($post_id) {
		if(!isset($_POST["secondary_post_title"])) {
			return false;
		}

		if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
			return false;
		}

		if(isset($_POST["post_type"]) && "page" == $_POST["post_type"]) {
			if(!current_user_can("edit_page", $post_id)) {
				return false;
			}
		}
		else {
			if(!current_user_can("edit_post", $post_id)) {
				return false;
			}
		}

		return update_post_meta($post_id, "_secondary_title", stripslashes(esc_attr($_POST["secondary_post_title"])));
	}

	add_action("save_post", "secondary_title_edit_post");

	/**
	 * Updates the secondary title in quick edit and
	 * in bulk actions.
	 *
	 * @since 0.9
	 *
	 * @param $post_id
	 */
	function secondary_title_edit_post_quick_edit($post_id) {
		/** Stop script if it is called through bulk action or when the secondary title isn't set */
		if(isset($_GET["_status"]) || !isset($_POST["secondary_title"])) {
			return $post_id;
		}
		update_post_meta($post_id, "_secondary_title", stripslashes(esc_attr($_POST["secondary_title"])));

		return $post_id;
	}

	add_action("edit_post", "secondary_title_edit_post_quick_edit");

	/**
	 * Adds a "Secondary title" column to the posts/pages
	 * overview (edit.php).
	 *
	 * @since 0.7
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	function secondary_title_overview_columns($columns) {
		$new_columns = array();
		foreach($columns as $column_slug => $column_title) {
			/** Insert the secondary title before the "author" column */
			if($column_slug == "author") {
				$new_columns["secondary_title"] = __("Secondary title", "secondary_title");
			}
			$new_columns[$column_slug] = $column_title;
		}

		return $new_columns;
	}

	/**
	 * Displays the extra column for the post types for which
	 * the secondary title has been activated.
	 *
	 * @since 0.7
	 */
	function secondary_title_init_columns() {
		$allowed_post_types = secondary_title_get_setting("post_types");
		$post_types         = get_post_types(
			array(
				"public" => true
			)
		);
		foreach($post_types as $post_type) {
			/** Add "Secondary title" column to activated post types */
			if(!isset($allowed_post_types[0]) || in_array($post_type, $allowed_post_types, false)) {
				/** Adding columns */
				add_filter("manage_{$post_type}_posts_columns", "secondary_title_overview_columns");
				/** Adding columns content */
				add_filter("manage_{$post_type}_posts_custom_column", "secondary_title_overview_column_content", 10, 2);
			}
		}
	}

	/** Display the column unless deactivated by filter */
	if(apply_filters("secondary_title_show_overview_column", true)) {
		add_action("admin_init", "secondary_title_init_columns");
	}

	/**
	 * Displays the secondary title and lets
	 * jQuery move it into the column.
	 *
	 * @param $column
	 * @param $post_id
	 *
	 * @since 0.7
	 */
	function secondary_title_overview_column_content($column, $post_id) {
		if($column == "secondary_title") {
			the_secondary_title($post_id);
		}
	}

	/**
	 * @param $column_name
	 *
	 * @since 1.5.4
	 */
	function secondary_title_overview_quick_edit($column_name) {
		if($column_name !== "secondary_title") {
			return;
		}

		echo '<label class="secondary-title-quick-edit-label">';
		echo '<span class="title">' . __("Sec. title", "secondary_title") . '</span>';
		echo '<span class="input-text-wrap"><input type="text" name="secondary_title" value="" /></span>';
		echo "</label>";
	}

	add_action("quick_edit_custom_box", "secondary_title_overview_quick_edit", 10);

	/**
	 * If auto show function is set, replace the post titles
	 * with custom title format.
	 *
	 * @since 0.1
	 *
	 * @param $title
	 *
	 * @return mixed
	 */
	function secondary_title_auto_show($title) {
		global $post;

		/** Keep the standard title */
		$standard_title = $title;

		/** Don't do "auto show" when on admin area or if the post is not a valid post */
		if(!isset($post->ID) || is_admin()) {
			return $standard_title;
		}

		$secondary_title = get_secondary_title($post->ID, "", "", true);

		/** Validate secondary title */
		if(!$secondary_title || get_option("secondary_title_auto_show") == "off" || $title != wptexturize($post->post_title) || is_admin()) {
			return $standard_title;
		}

		/** Apply title format */
		$format = str_replace('"', "'", stripslashes(get_option("secondary_title_title_format")));
		$title  = str_replace("%title%", $title, $format);
		$title  = str_replace("%secondary_title%", html_entity_decode($secondary_title), $title);

		/** Only display if title is within the main loop */
		if(secondary_title_get_setting("only_show_in_main_post") == "on") {
			global $wp_query;
			if(!$wp_query->in_the_loop) {
				return $standard_title;
			}
		}

		return $title;
	}

	add_filter("the_title", "secondary_title_auto_show");

	/**
	 * Loads scripts and styles.
	 *
	 * @since 0.1
	 */
	function secondary_title_scripts_and_styles() {
		$plugin_folder = plugin_dir_url(dirname(__FILE__));
		wp_enqueue_script("secondary-title-script-admin", "$plugin_folder/scripts/admin.js");
		wp_enqueue_style("secondary-title-style-admin", "$plugin_folder/styles/admin.css");
	}

	add_action("admin_enqueue_scripts", "secondary_title_scripts_and_styles");

	/**
	 * Initialize setting on admin interface.
	 *
	 * @since 0.1
	 */
	function init_admin_settings() {
		/** Creates a new page on the admin interface */
		add_options_page(__("Settings", "secondary_title"), "Secondary Title", "manage_options", "secondary-title", "secondary_title_settings_page");
	}

	add_action("admin_menu", "init_admin_settings");

	/**
	 * Registers the %secondary_title% tag as a
	 * permalink tag.
	 *
	 * @since 0.8
	 */
	function secondary_title_permalinks_init() {
		if(secondary_title_get_setting("use_in_permalinks") === "custom") {
			add_rewrite_tag("%secondary_title%", "([^&]+)");
		}
	}

	add_action("init", "secondary_title_permalinks_init");

	/**
	 * @param $permalink
	 * @param $post
	 *
	 * @since 1.5.4
	 *
	 * @return mixed
	 **/
	function secondary_title_modify_permalink($permalink, $post) {
		$secondary_title         = get_secondary_title($post->ID);
		$secondary_title_setting = secondary_title_get_setting("use_in_permalinks");

		/** Don't do anything if option is not activated */
		if($secondary_title_setting !== "custom") {
			return $permalink;
		}
		$placeholder = "%secondary_title%";

		if($secondary_title) {
			$permalink = str_replace($placeholder, $secondary_title, $permalink);
		}
		else {
			/** Remove placeholder from permalink if no secondary title exists */
			$permalink = str_replace($placeholder, "", $permalink);
		}

		return $permalink;
	}

	add_filter("post_link", "secondary_title_modify_permalink", 10, 2);