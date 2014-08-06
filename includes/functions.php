<?php
	/*
	 * This file contains the main functions that can be used to return,
	 * display or modify every information that is related to the plugin.
	 *
	 * @package Secondary Title
	 * @subpackage Global
	 */

	/**
	 * Returns the plugin's default settings and their values.
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	function get_secondary_title_default_settings() {
		/** Define the default settings and their values */
		$default_settings = array(
			"secondary_title_post_types"             => array(),
			"secondary_title_categories"             => array(),
			"secondary_title_post_ids"               => array(),
			"secondary_title_auto_show"              => "on",
			"secondary_title_only_show_in_main_post" => "on",
			"secondary_title_title_format"           => "%secondary_title%: %title%",
			"secondary_title_title_input_position"   => "above"
		);
		return $default_settings;
	}

	/**
	 * Returns a single default setting and its value.
	 *
	 * @since 0.1
	 *
	 * @param string $setting
	 *
	 * @return array
	 */
	function get_secondary_title_default_setting($setting) {
		/** Setting up default settings and values */
		$default_settings = get_secondary_title_default_settings();
		/** Check if parameter is set; else use default setting value */
		if(!empty($setting)) {
			if(isset($default_settings[$setting])) {
				$default_settings = $default_settings[$setting];
			}
			else {
				$default_settings = false;
			}
		}
		return $default_settings;
	}

	/**
	 * Returns a specific setting for the plugin. If the selected
	 * option is unset, the default value will be returned.
	 *
	 * @since 0.1
	 *
	 * @param $setting
	 *
	 * @return array|mixed|void
	 */
	function get_secondary_title_setting($setting) {
		$setting_name    = "secondary_title_" . $setting;
		$option_setting  = get_option($setting_name);
		$default_setting = get_secondary_title_default_setting($setting_name);

		/** Use default value if setting is not set */
		if(empty($setting)) {
			$setting = $default_setting;
		}
		else {
			$setting = $option_setting;
		}
		return $setting;
	}

	/**
	 * Returns the IDs of the posts for which secondary title is activated.
	 *
	 * @since 0.1
	 *
	 * @return array|mixed|void Post IDs
	 */
	function get_secondary_title_post_ids() {
		return get_secondary_title_setting("post_ids");
	}

	/**
	 * Returns the post types for which secondary title is activated.
	 *
	 * @since 0.1
	 *
	 * @return array|mixed|void Post types
	 */
	function get_secondary_title_post_types() {
		return get_secondary_title_setting("post_types");
	}

	/**
	 * Returns the categories for which secondary title is activated.
	 *
	 * @since 0.1
	 *
	 * @return array|mixed|void Selected categories
	 */
	function get_secondary_title_post_categories() {
		return get_secondary_title_setting("categories");
	}

	/**
	 * Get the secondary title from post ID $post_id
	 *
	 * @since 0.1
	 *
	 * @param int    $post_id ID of target post
	 *
	 * @param string $suffix  To be added after the secondary title
	 * @param string $prefix  To be added in front of the secondary title
	 *
	 * @return mixed The secondary title
	 */
	function get_secondary_title($post_id = 0, $prefix = "", $suffix = "") {
		/** If $post_id not set, use current post ID */
		if(!$post_id) {
			$post_id = get_the_ID();
		}
		$post_ids = get_secondary_title_post_ids();
		if(count($post_ids) != 0 && !in_array($post_id, $post_ids)) {
			return false;
		}
		/** Return the secondary title */
		$secondary_title = $prefix . get_post_meta($post_id, "_secondary_title", true) . $suffix;

		/** Apply filters to secondary title if used with Word Filter Plus plugin */
		if(class_exists("WordFilter")) {
			$word_filter     = new WordFilter;
			$secondary_title = $word_filter->filter_title($secondary_title);
		}

		return $secondary_title;
	}

	/**
	 * Prints the secondary title and adds an optional suffix.
	 *
	 * @since 0.1
	 *
	 * @param int    $post_id ID of target post
	 * @param string $suffix  To be added after the secondary title
	 * @param string $prefix  To be added in front of the secondary title
	 */
	function the_secondary_title($post_id = 0, $prefix = "", $suffix = "") {
		echo get_secondary_title($post_id, $prefix, $suffix);
	}

	/**
	 * Returns the secondary title link.
	 *
	 * @since 0.5
	 *
	 * @param int   $post_id ID of the target post.
	 * @param array $options Additional options.
	 *
	 * @return string
	 */
	function get_secondary_title_link($post_id = 0, $options = array()) {
		if(!$post_id) {
			$post_id = get_the_ID();
		}
		$default_options = array(
			"before_link" => "",
			"after_link"  => "",
			"before_text" => "",
			"after_text"  => "",
			"link_target" => "_self",
			"link_title"  => "",
			"link_id"     => "secondary-title-link-" . $post_id,
			"link_class"  => "secondary-title-link"
		);
		foreach($default_options as $default_option => $value) {
			if(!isset($options[$default_option])) {
				$options[$default_option] = $value;
			}
		}
		$link_attributes = array(
			"title",
			"id",
			"class",
			"target"
		);
		/** Build and attach attributes if not empty */
		foreach($link_attributes as $link_attribute) {
			$link_attribute_full = "link_" . $link_attribute;
			if(!empty($options[$link_attribute_full])) {
				$options[$link_attribute_full] = ' ' . $link_attribute . '="' . $options[$link_attribute_full] . '"';
			}
		}

		$secondary_title = get_secondary_title($post_id);

		/** Glue together and build the actual link */
		$link = $options["before_link"];
		$link .= '<a href="' . get_permalink($post_id) . '"' . $options["link_target"] . $options["link_title"] . $options["link_id"] . $options["link_class"] . '>';
		$link .= $options["before_text"];
		$link .= $secondary_title;
		$link .= $options["after_text"];
		$link .= "</a>";
		$link .= $options["after_link"];

		return $link;
	}

	/**
	 * Displays the secondary title link.
	 *
	 * @since 0.5
	 *
	 * @param int   $post_id
	 * @param array $options
	 */
	function the_secondary_title_link($post_id = 0, $options = array()) {
		echo get_secondary_title_link($post_id, $options);
	}

	/**
	 * Returns whether the specified post has a
	 * secondary title or not.
	 *
	 * @since 0.5.1
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	function has_secondary_title($post_id = 0) {
		$secondary_title = get_secondary_title($post_id);
		$has             = false;
		if(!empty($secondary_title)) {
			$has = true;
		}
		return $has;
	}

	/**
	 * Returns all available post types except pages, attachments,
	 * revision ans nav_menu_items.
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	function get_filtered_post_types() {
		/** Returns all registered post types */
		$post_types = get_post_types(array(
			"public" => true, // Only show post types that are publicly accessible in the front end
		));

		$output = array();
		/** Filter out the attachment post type */
		foreach($post_types as $post_type) {
			if($post_type != "attachment") {
				array_push($output, $post_type);
			}
		}
		return $output;
	}

	/**
	 * Sends me an e-mail when a bug report is filed and sent.
	 * To be called as jQuery include which is why the script is
	 * being cancelled after execution ("return false;").
	 *
	 * @since 0.7
	 *
	 * @return bool
	 */
	function secondary_title_send_bug_report() {

	}