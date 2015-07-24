<?php
	/**
	 * This file contains the functions used within the admin area.
	 * The code for the plugin's settings page is stored separately within /includes/settings.php.
	 *
	 * @package    Secondary Title
	 * @subpackage Administration
	 */

	/**
	 * Stop script when the file is called directly.
	 */
	if(!function_exists("add_action")) {
		return false;
	}

	/**
	 * Build the - invisible - secondary title input on edit pages
	 * to let jQuery displaying it (see admin.js).
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	function init_secondary_title_admin_posts() {
		$current_screen = get_current_screen();

		/** Don't insert the text input when not viewing the post or edit page */
		if($current_screen->base != "post" && $current_screen->base != "edit" || strstr($current_screen->id, "edit-")) {
			return false;
		}

		$post_types = get_option("secondary_title_post_types");
		$post_ids   = get_option("secondary_title_post_ids");

		if(is_array($post_types) || is_array($post_ids)) {
			/** Stop script when post_type is not among the set post types, same with post IDs */
			if((count($post_types) && !in_array(get_post_type(), $post_types, false)) || (count($post_ids) != 0 && !in_array(get_the_ID(), $post_ids, false))) {
				return false;
			}
		}

		$title_input_position = secondary_title_get_setting("title_input_position");

		?>
		<span id="hide-secondary-title" hidden="hidden">
		<input type="hidden" id="secondary-title-input-position" value="<?php echo $title_input_position; ?>"/>
		<input type="text" size="30" id="secondary-title-input" placeholder="<?php _e("Enter secondary title here", "secondary_title"); ?>" name="secondary_post_title" value="<?php echo get_post_meta(get_the_ID(), "_secondary_title", true); ?>"/>
		</span>
		<?php
		return true;
	}

	add_action("admin_head", "init_secondary_title_admin_posts");