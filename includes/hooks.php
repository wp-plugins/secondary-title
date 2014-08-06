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
	 * Loads the text domain for localization.
	 *
	 * @since 0.1
	 */
	function secondary_title_init_languages() {
		load_plugin_textdomain("secondary_title", false, dirname(plugin_basename(__FILE__)) . "/../languages/");
	}

	add_action("init", "secondary_title_init_languages");

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
		$screen = get_current_screen();
		/** Only update if we're on the edit screen */
		if(isset($screen->base) && $screen->base == "post") {
			update_post_meta($post_id, "_secondary_title", $_POST["secondary_title"]);
		}
	}

	add_action("edit_post", "secondary_title_edit_post");

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
		update_post_meta($post_id, "_secondary_title", $_POST["secondary_title"]);
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
			else {
				$new_columns[$column_slug] = $column_title;
			}
		}

		return $new_columns;
	}

	/**
	 * Only add secondary title column to allowed post types.
	 *
	 * @since 0.7
	 */
	function secondary_title_init_columns() {
		$allowed_post_types = get_secondary_title_setting("post_types");
		/** Display for all post types if setting is empty or invalid */
		if(!is_array($allowed_post_types) || !count($allowed_post_types)) {
			add_filter("manage_posts_columns", "secondary_title_overview_columns");
			add_filter("manage_page_posts_columns", "secondary_title_overview_columns");
		}
		else {
			/** Display only for set post types */
			foreach($allowed_post_types as $post_type) {
				add_filter("manage_" . $post_type . "_posts_columns", "secondary_title_overview_columns");
			}
		}
	}

	add_action("admin_init", "secondary_title_init_columns");

	/**
	 * @param $column
	 * @param $post_id
	 *
	 * @since 0.7
	 */
	function secondary_title_overview_column_content($column, $post_id) {
		if($column == "secondary_title") {
			the_secondary_title($post_id);
			echo '<label class="secondary-title-quick-edit-label" hidden="hidden">';
			echo '<span class="title">' . __("Sec. title", "secondary_title") . '</span>';
			echo '<span class="input-text-wrap"><input type="text" name="secondary_title" value="' . get_secondary_title($post_id) . '" /></span>';
			echo "</label>";
		}
	}

	add_filter("manage_posts_custom_column", "secondary_title_overview_column_content", 10, 2);

	/**
	 * If auto show function is set, replace the post titles with custom title format.
	 *
	 * @since 0.1
	 *
	 * @param $title
	 *
	 * @return mixed
	 */
	function secondary_title_auto_show($title) {
		/** Don't do "auto show" when on admin area */
		if(is_admin()) {
			return $title;
		}
		global $post;
		/** Keep the standard title */
		$standard_title = $title;
		/** Insert the secondary title in the admin interface */
		/** Get post information */
		$post_category = get_the_category();
		if(isset($post_category[0]->slug)) {
			$post_category = $post_category[0]->slug;
		}
		else {
			$post_category = array();
		}
		/** Checks if auto show function is set and the secondary title is not empty */
		if(get_option("secondary_title_auto_show") == "on" && get_secondary_title() != "" && $title == wptexturize($post->post_title) || is_admin()) {
			$post_ids        = get_secondary_title_post_ids();
			$post_types      = get_secondary_title_post_types();
			$post_categories = get_secondary_title_post_categories();
			/** Stop script if it does not match the set options */
			if(count($post_ids) != 0 && !in_array(get_the_ID(), $post_ids) || count($post_types) != 0 && !in_array(get_post_type(), $post_types) || count($post_categories) != 0 && !in_array($post_category, $post_categories)) {
			}
			else {
				/** Apply title format */
				$format = str_replace('"', "'", stripslashes(get_option("secondary_title_title_format")));
				$title  = str_replace("%title%", $title, $format);
				$title  = str_replace("%secondary_title%", get_secondary_title(), $title);
			}
		}
		/** Only display if title is within the main lop */
		if(get_secondary_title_setting("only_show_in_main_post") == "on") {
			global $wp_query;
			if(!$wp_query->in_the_loop) {
				return $standard_title;
			}
		}
		return $title;
	}

	add_filter("the_title", "secondary_title_auto_show", 999, 2);

	/**
	 * Loads scripts and styles.
	 *
	 * @since 0.1
	 */
	function secondary_title_scripts_and_styles() {
		$plugin_folder  = str_replace("\\", "/", plugin_dir_url(dirname(__FILE__)));
		$scripts_folder = $plugin_folder . "scripts/";
		$styles_folder  = $plugin_folder . "styles/";
		if(is_admin()) {
			wp_enqueue_script("secondary-title-script-admin", $scripts_folder . "admin.js", array(), "1.0", true);
			wp_enqueue_style("secondary-title-style-admin", $styles_folder . "admin.css");
		}
	}

	add_action("admin_enqueue_scripts", "secondary_title_scripts_and_styles");

	/**
	 * Registers the %secondary_title% tag as a
	 * permalink tag.
	 *
	 * @since 0.8
	 */
	function secondary_title_permalinks_init() {
		add_rewrite_tag("%secondary_title%", "([^&]+)");
	}

	add_action("init", "secondary_title_permalinks_init");

	/**
	 * @param $permalink
	 * @param $post
	 *
	 * @since 0.8
	 *
	 * @return mixed
	 */
	function secondary_title_permalinks($permalink, $post) {
		$setting                   = get_secondary_title_setting("use_in_permalinks");
		$secondary_title           = get_secondary_title($post->ID);
		$secondary_title_sanitized = sanitize_title($secondary_title);
		if($setting == "auto" && !empty($secondary_title)) {
			$permalink = str_replace($post->post_name, $secondary_title_sanitized . "-" . $post->post_name, $permalink);
		}
		elseif($setting == "custom" && !empty($secondary_title)) {
			$permalink = str_replace("%secondary_title%", $secondary_title_sanitized, $permalink);
		}
		else {
			$permalink = str_replace("%secondary_title%", "", $permalink);
		}

		/** Remove possible double slash */
		$permalink_ending = substr($permalink, strlen($permalink) - 2, strlen($permalink));
		if($permalink_ending == "//") {
			$permalink = substr($permalink, 0, strlen($permalink) - 1);
		}
		return $permalink;
	}

	add_filter("post_link", "secondary_title_permalinks", 10, 2);