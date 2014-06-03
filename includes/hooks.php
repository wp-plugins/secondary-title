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
	 * Loads the text domain for localization.
	 *
	 * @since 0.1
	 */
	function init_secondary_title_languages() {
		load_plugin_textdomain("secondary_title", false, dirname(plugin_basename(__FILE__)) . "/languages/");
	}

	add_action("init", "init_secondary_title_languages");

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
		/** Keep the standard title */
		$standard_title = $title;
		/** Insert the secondary title in the admin interface */
		/** Get post information */
		global $post;
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
				$format = str_replace('"', "'", get_option("secondary_title_title_format"));
				$title  = str_replace("%title%", $title, $format);
				$title  = str_replace("%secondary_title%", get_secondary_title(), $title);
				$title  = stripslashes($title);
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

	add_filter("the_title", "secondary_title_auto_show", 10, 2);

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
			wp_enqueue_script("scripts-admin", $scripts_folder . "admin.js", array(), "1.0", true);
			wp_enqueue_style("styles-admin", $styles_folder . "admin.css");
		}
	}

	add_action("admin_enqueue_scripts", "secondary_title_scripts_and_styles");

	/**
	 * Adds a column for the secondary title to the posts/page overview list.
	 *
	 * @since    0.7
	 *
	 * @param $columns
	 *
	 * @internal param $column_name
	 *
	 * @internal param $columns
	 *
	 * @return mixed
	 */
	function secondary_title_overview_column($columns) {
		$new_columns = array();
		/** Re-build the columns and insert the secondary title columns after the title column */
		foreach($columns as $column_name => $column_title) {
			if($column_name == "author") {
				$new_columns["secondary_title"] = __("Secondary title", "secondary_title");
			}
			else {
				$new_columns[$column_name] = $column_title;
			}
		}

		return $new_columns;
	}

	/**
	 * Fills the secondary title column with content.
	 *
	 * @since 0.7
	 *
	 * @param $column_name
	 */
	function secondary_title_overview_column_content($column_name) {
		if($column_name == "secondary_title") {
			echo '<strong><a href="' . get_edit_post_link() . '" title="' . sprintf(__("Edit &quot;%s&quot;", "secondary_title"), get_the_title()) . '" class="row-title">' . get_secondary_title() . "</a></strong>";
		}
	}

	/** Only display secondary title for selected post types. If empty, use all */
	$selected_post_types = get_secondary_title_setting("post_types");
	$post_types          = get_filtered_post_types();
	if(count($selected_post_types) > 0) {
		$post_types = $selected_post_types;
	}
	foreach($post_types as $post_type) {
		$post_type_labels = get_post_type_labels(get_post_type_object($post_type));
		$post_type_plural = sanitize_title_for_query($post_type_labels->name);
		add_action("manage_" . $post_type . "_posts_columns", "secondary_title_overview_column", 10, 2);
		add_action("manage_" . $post_type_plural . "_custom_column", "secondary_title_overview_column_content");
	}

	/**
	 * @since 0.7
	 *
	 * @param $column_name
	 */
	function secondary_title_overview_quick_edit($column_name) {
		if($column_name == "secondary_title") {
			?>
			<label>
				<span class="title"><?php _e("Sec. title", "secondary_title"); ?></span>
				<span class="input-text-wrap"><input type="text" name="secondary_title" class="ptitle quick_edit_secondary_title_input" value=""></span>
			</label>
		<?php
		}
	}

	add_action("quick_edit_custom_box", "secondary_title_overview_quick_edit");

	/**
	 * @since 0.7
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	function secondary_title_overview_quick_edit_save($post_id) {
		if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
			return $post_id;
		}
		//$post = get_post($post_id);
		if(!empty($_POST["secondary_title"])) {
			update_post_meta($post_id, "_secondary_title", $_POST["secondary_title"]);
		}
		return $post_id;
	}

	add_action("save_post", "secondary_title_overview_quick_edit_save");