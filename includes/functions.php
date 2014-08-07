<?php
	/*
	 * This file contains the main functions that can be used to return,
	 * display or modify every information that is related to the plugin.
	 *
	 * @package Secondary Title
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
	 * @return array|mixed|void
	 *
	 * @since 0.1
	 */
	function get_secondary_title_default_settings() {
		/** Define the default settings and their values */
		$default_settings = array(
			"secondary_title_post_types"             => array(),
			"secondary_title_categories"             => array(),
			"secondary_title_post_ids"               => array(),
			"secondary_title_auto_show"              => "on",
			"secondary_title_title_format"           => "%secondary_title%: %title%",
			"secondary_title_title_input_position"   => "above",
			"secondary_title_use_in_permalinks"      => "off",
			"secondary_title_only_show_in_main_post" => "on"
		);
		$default_settings = apply_filters("get_get_secondary_title_default_settings", $default_settings);
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
	 * @return array
	 */
	function get_secondary_title_settings() {
		$settings = array();
		foreach(get_secondary_title_default_settings() as $setting => $default_value) {
			$option = get_option($setting);
			if(empty($option)) {
				$value = $default_value;
			}
			else {
				$value = $option;
			}
			$settings[$setting] = $value;
		}
		return $settings;
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
		$settings = get_secondary_title_settings();
		$setting  = $settings["secondary_title_" . $setting];
		return $setting;
	}

	function reset_secondary_title_settings() {
		$settings = get_secondary_title_settings();
		foreach($settings as $setting => $value) {
			delete_option($setting);
		}
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
		$post_ids   = get_secondary_title_post_ids();
		$post_types = get_secondary_title_post_types();

		/** Stop if post is not among the allowed post types/IDs */
		if(count($post_ids) != 0 && !in_array($post_id, $post_ids) || count($post_types) != 0 && !in_array(get_post_type($post_id), $post_types)) {
			return false;
		}

		$secondary_title = get_post_meta($post_id, "_secondary_title", true);
		/** Return the secondary title if exists */
		if(!empty($secondary_title)) {
			$secondary_title = $prefix . $secondary_title . $suffix;
		}
		else {
			return false;
		}

		/** Apply filters to secondary title if used with Word Filter Plus plugin */
		if(class_exists("WordFilter")) {
			$word_filter     = new WordFilter;
			$secondary_title = $word_filter->filter_title($secondary_title);
		}
		$secondary_title = apply_filters("get_secondary_title", $secondary_title, $post_id, $prefix, $suffix);
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
		$secondary_title = get_secondary_title($post_id, $prefix, $suffix);
		$secondary_title = apply_filters("the_secondary_title", $secondary_title, $post_id, $prefix, $suffix);
		echo $secondary_title;
	}

	/**
	 * Returns the secondary title link.
	 *
	 * @since 0.5
	 *
	 * @param int    $post_id ID of the target post.
	 * @param string $wrapper HTML element around link.
	 * @param array  $options Additional options.
	 *
	 * @return string
	 */
	function get_secondary_title_link($post_id = 0, $wrapper = "", $options = array()) {
		if(!$post_id) {
			$post_id = get_the_ID();
		}
		/** Define default options used if not set */
		$default_options = array(
			"before_link" => "",
			"after_link"  => "",
			"before_text" => "",
			"after_text"  => "",
			"link_text"   => get_secondary_title($post_id),
			"link_target" => "_self",
			"link_title"  => sprintf(__("Go to &quot;%s&quot;"), get_the_title($post_id)),
			"link_id"     => "secondary-title-link-" . $post_id,
			"link_class"  => "secondary-title-link"
		);
		foreach($default_options as $default_option => $value) {
			if(!isset($options[$default_option])) {
				$options[$default_option] = $value;
			}
		}

		$link = apply_filters("get_secondary_title_link", $post_id, $wrapper, $options);

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

		$wrapper_start = "";
		$wrapper_end   = "";
		/** Build the wrapper if set */
		if(!empty($wrapper)) {
			$wrapper_start = "<" . $wrapper . ">";
			$wrapper_end   = "</" . $wrapper . ">";
		}

		/** Glue together and build the actual link */
		$link .= $wrapper_start;
		$link .= $options["before_link"];
		$link .= '<a href="' . get_permalink($post_id) . '"' . $options["link_target"] . $options["link_title"] . $options["link_id"] . $options["link_class"] . '>';
		$link .= $options["before_text"];
		$link .= $options["link_text"];
		$link .= $options["after_text"];
		$link .= "</a>";
		$link .= $options["after_link"];
		$link .= $wrapper_end;
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
		$link = get_secondary_title_link($post_id, $options);
		echo $link;
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
	function get_secondary_title_filtered_post_types() {
		/** Returns all registered post types */
		$post_types = get_post_types(array(
			"public" => true, // Only show post types that are publicly accessible in the front end
		));

		return $post_types;
	}