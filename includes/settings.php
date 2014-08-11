<?php
	/*
	 * This file handles everything within the "Settings" > "Secondary Title"
	 * settings page within the admin area.
	 *
	 * @package       Secondary Title
	 * @subpackage    Administration
	 */

	/**
	 * Stop script when the file is called directly.
	 */
	if(!function_exists("add_action")) {
		return false;
	}

	/**
	 * Initialize setting on admin interface.
	 *
	 * @since 0.1
	 */
	function init_admin_settings() {
		/** Creates a new page on the admin interface */
		add_options_page(__("Secondary Title settings"), "Secondary Title", "manage_options", "secondary_title", "build_admin_settings");
	}

	add_action("admin_menu", "init_admin_settings");

	/**
	 * Build the option page.
	 *
	 * @since 0.1
	 */
	function build_admin_settings() {
		/** Check if the submit button was hit */
		if(isset($_POST["submitted"])) {
			/** Define fields that are to be saved */
			$fields = array(
				"post_types",
				"categories",
				"post_ids",
				"auto_show",
				"title_format",
				"title_input_position",
				"only_show_in_main_post",
				"use_in_permalinks",
			);
			foreach($fields as $field) {
				$submitted_value = "";
				if(isset($_POST[$field])) {
					$submitted_value = $_POST[$field];
				}
				if($field == "post_types" && empty($submitted_value) || $field == "categories" && empty($submitted_value) || $field == "post_ids" && empty($submitted_value)) {
					$submitted_value = array();
				}
				if($field == "post_ids" && is_string($submitted_value)) {
					$submitted_value = preg_replace("'[^0-9,.]'", "", $submitted_value);
					$submitted_value = explode(",", $submitted_value);
				}
				update_option("secondary_title_" . $field, $submitted_value);
			}
			/** Displays message when settings are saved */
			echo '<div id="message" class="updated"><p><strong>' . __("Settings saved.", "secondary_title") . '</strong></p></div>';
		}

		/**
		 * Build the actual settings page.
		 */
		?>
		<div class="wrap">
		<h2><?php echo __("Settings", "secondary_title") . " › Secondary Title"; ?></h2>
		<?php
		/** Get a random post with a secondary title */
		$random_post = get_random_post_with_secondary_title();
		if($random_post) {
			/** @noinspection PhpUndefinedFieldInspection */
			$preview_title = $random_post->post_title;
			/** @noinspection PhpUndefinedFieldInspection */
			$preview_secondary_title = get_secondary_title($random_post->ID);
		}
		else {
			$preview_title           = __("Malaysian Airlines flight MH370 lost over Gulf of Thailand", "secondary_title");
			$preview_secondary_title = __("Plane missing", "secondary_title");
		}
		?>
		<!-- Additional information provided for jQuery -->
		<input type="hidden" id="preview-label" value="<?php _e("Preview", "secondary_title"); ?>"/>
		<input type="hidden" id="preview-title" value="<?php echo $preview_title; ?>"/>
		<input type="hidden" id="preview-secondary-title" value="<?php echo $preview_secondary_title; ?>"/>

	<form method="post" id="secondary-title-settings" action="">
	<table class="form-table">
	<tbody>
	<tr>
		<th scope="row">
			<label for="post_types"><?php _e("Post types", "secondary_title"); ?></label>
		</th>
		<td>
			<fieldset>
				<?php
					/** Get filtered post types and set up variables */
					$filtered_post_types = get_secondary_title_filtered_post_types();
					$post_types = get_secondary_title_post_types();
					$checked = "";
					$counter = 0;

					foreach($filtered_post_types as $post_type) {
						/** Checks whether the displayed post type is set */
						$post_type = get_post_type_object($post_type);
						if(is_array($post_types)) {
							if(in_array($post_type->name, $post_types)) {
								/** Add HTML checked attribute */
								$checked = " checked";
							}
						}
						?>
						<input type="checkbox" name="post_types[]" id="<?php echo $post_type->name; ?>" value="<?php echo $post_type->name; ?>"<?php echo $checked;
							$checked = ""; ?> />
						<label for="<?php echo $post_type->name; ?>"><?php echo $post_type->labels->name; ?></label>
						<br/>
						<?php
						$counter++;
					}
				?>
				<p>
					<small>
						<a href="#" title="<?php _e("Select all", "secondary_title"); ?>" class="select-all"><?php _e("Select all", "secondary_title"); ?></a>
						<a href="#" title="<?php _e("Unselect all", "secondary_title"); ?>" class="unselect-all" hidden="hidden"><?php _e("Unselect all", "secondary_title"); ?></a>
					</small>
				</p>
				<p class="description"><?php _e("Post types for which secondary titles should be activated.<br /> Select none to use all available post types.", "secondary_title"); ?></p>
			</fieldset>
		</td>
	<tr>
		<th scope="row">
			<label for="categories"><?php _e("Categories", "secondary_title"); ?></label>
		</th>
		<td>
			<fieldset id="categories-list">
				<?php
					/** Show empty categories, too */
					$categories = get_terms("category", array(
						"hide_empty" => false
					));
					$counter = 0;
					$batch_counter = 0;
					$non_empty_count = 0;
					foreach($categories as $category) {
						$counter++;
						$batch_counter++;
						if($category->count >= 1) {
							$non_empty_count++;
						}
						if($batch_counter == 1) {
							echo '<ul class="category-batch">';
						}
						$checked             = "";
						$selected_categories = get_option("secondary_title_categories");
						if(is_array($selected_categories) && in_array($category->slug, $selected_categories)) {
							$checked = " checked";
						}
						echo '<li>';
						echo '<input type="checkbox" name="categories[' . $counter . ']" id="category-' . $category->slug . '" value="' . $category->slug . '"' . $checked . ' />';
						echo '<label for="category-' . $category->slug . '">' . $category->name . ' (<span class="count">' . $category->count . '</span>)</label>';
						echo "</li>";
						if($batch_counter == 10 || $counter == count($categories)) {
							echo '</ul>';
							$batch_counter = 0;
						}
					}
				?>
				<div class="clear"></div>
				<p>
					<small>
						<a href="#" title="<?php _e("Select all", "secondary_title"); ?>" class="select-all"><?php _e("Select all", "secondary_title"); ?></a>
						<a href="#" title="<?php _e("Unselect all", "secondary_title"); ?>" class="unselect-all" hidden="hidden"><?php _e("Unselect all", "secondary_title"); ?></a>
						|
						<a href="#" class="select-non-empty"><?php echo __("Select non-empty categories", "secondary_title"); ?></a>
					</small>
				</p>
				<p class="description"><?php _e("Categories for which secondary titles should be activated.<br /> Select none to use all available categories.", "secondary_title"); ?></p>
			</fieldset>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="post_ids"><?php _e("Post IDs", "secondary_title"); ?></label>
		</th>
		<td>
			<input type="text" id="post_ids" class="regular-text" name="post_ids" placeholder="<?php _e("E.g.: 4, 28, 104", "secondary_title"); ?>" value="<?php echo implode(", ", get_secondary_title_post_ids()); ?>"/>
			<br/>

			<p class="description"><?php _e("Only use secondary title for specific posts. Separate IDs with commas.", "secondary_title"); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="auto-show-radio-on"><?php _e("Insert automatically", "secondary_title"); ?></label>
		</th>
		<td>
			<fieldset id="auto-show-fieldset">
				<input type="radio" name="auto_show" id="auto-show-radio-on" value="on"<?php if(get_option("secondary_title_auto_show") == "on" || get_option("secondary_title_auto_show") == "") {
					echo " checked";
				} ?> />
				<label for="auto-show-radio-on"><?php _e("Yes", "secondary_title"); ?></label>

				<input type="radio" name="auto_show" id="auto-show-radio-off" value="off"<?php if(get_option("secondary_title_auto_show") == "off") {
					echo " checked";
				} ?> />
				<label for="auto-show-radio-off"><?php _e("No", "secondary_title"); ?></label>
				<?php
					$readme_url = plugin_dir_url(__FILE__) . "../readme.txt";
					$auto_show_off_description = sprintf(__('To manually insert the secondary title in your theme, use <code>&lt;?php get_secondary_title(); ?&gt;</code><br />or <code>&lt;?php the_secondary_title(); ?&gt;</code>. See <a href="%s" title="Open readme.txt" target="_blank">readme.txt</a> or the <a href="%s" title="See official documentation" target="_blank" >official documentation</a> for additional parameters.', "secondary_title"), $readme_url, "http://www.koljanolte.com/wordpress/plugins/secondary-title/#Parameters");
				?>
				<p id="auto-show-on-description" class="description"><?php _e("Automatically merges the secondary title with the standard title.", "secondary_title"); ?></p>
				<p id="auto-show-off-description" class="description" hidden="hidden"><?php echo $auto_show_off_description;
					?></p>
			</fieldset>
		</td>
	</tr>
	<tr id="title-format">
		<th scope="row">
			<label for="title-format-input"><?php _e("Title format", "secondary_title"); ?></label>
		</th>
		<td>
			<input type="text" name="title_format" id="title-format-input" class="regular-text" placeholder="<?php _e("E.g.: %secondary_title%: %title%", "secondary_title"); ?>" value="<?php echo stripslashes(esc_attr(get_option("secondary_title_title_format"))); ?>" autocomplete="off"/>
			<input type="button" class="button" id="button-reset" value="<?php _e("Reset", "secondary_title"); ?>"/>
			<p class="description">
				<?php echo sprintf(__('Use %s for the main title and %s for the secondary title.', "secondary_title"), '<code title="' . __("Add title to title format input", "secondary_title") . '">%title%</code>', '<code title="' . __("Add secondary title to title format input", "secondary_title") . '">%secondary_title%</code>'); ?>
			<p class="description" id="title_format_preview" hidden="hidden"></p><br/>

			<p class="description">
				<?php
					echo sprintf(__('<b>Note:</b> To style the output, use the <a href="%s" title="See an explanation on w3schools.com" target="_blank">style HTML attribute</a>, e.g.:<br />%s', "secondary_title"), "http://www.w3schools.com/tags/att_global_style.asp", '<code title="' . __("Add code to title format input", "secondary_title") . '">' . esc_attr('<span style="color:red;font-size:14px;">%secondary_title%</span>') . "</code>");
				?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="title_input_position_above"><?php _e("Title input position", "secondary_title"); ?></label>
		</th>
		<td>
			<?php $input_position = get_secondary_title_setting("title_input_position"); ?>
			<input type="radio" name="title_input_position" id="title_input_position_above" value="above"<?php
				if($input_position == "above") {
					echo ' checked="checked"';
				} ?> />
			<label for="title_input_position_above"><?php _e("Above", "secondary_title"); ?></label>
			<input type="radio" name="title_input_position" id="title_input_position_below" value="below"<?php
				if($input_position == "below") {
					echo ' checked="checked"';
				} ?> />
			<label for="title_input_position_below"><?php _e("Below", "secondary_title"); ?></label>

			<p class="description"><?php echo sprintf(__('Defines whether input field for the secondary title should be displayed above or below<br />the standard title <strong>within the add/edit post/page area</strong> on the admin interface.<br />See the <a href="%s" title="See the FAQ" target="_blank">FAQ</a> if you want to apply the same effect on your front end.', "secondary_title"), "http://www.koljanolte.com/wordpress/plugins/secondary-title/#faq-7"); ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row" id="only_show_in_main_post">
			<label for="only_show_in_main_post_yes"><?php _e("Only show in main post", "secondary_title"); ?></label>
		</th>
		<td>
			<fieldset id="only-show-in-main-posts-fieldset">
				<?php
					$checked = get_secondary_title_setting("only_show_in_main_post");
				?>
				<input type="radio" name="only_show_in_main_post" value="on" id="only_show_in_main_post_yes"<?php if($checked == "on") {
					echo ' checked="checked"';
				} ?> />
				<label for="only_show_in_main_post_yes"><?php _e("Yes", "secondary_title"); ?> </label>
				<input type="radio" name="only_show_in_main_post" value="off" id="only_show_in_main_post_no"<?php if($checked != "on") {
					echo ' checked="checked"';
				} ?> />
				<label for="only_show_in_main_post_no"><?php _e("No", "secondary_title"); ?></label>

				<p class="description"><?php _e("If activated, the secondary title will only be shown within the main post;<br /> sidebars, menu items etc. will be ignored. <strong>Only works when \"Insert automatically\" is enabled.</strong>", "secondary_title"); ?></p>
			</fieldset>
		</td>
	</tr>
	<tr id="use-in-permalinks-row">
		<th scope="row" id="use-in-permalinks">
			<label for="use-in-permalinks-auto"><?php _e("Use secondary title in permalinks", "secondary_title"); ?></label>
		</th>
		<td>
			<?php
				$permalinks_on = get_option("permalink_structure");
				$use_permalinks_option = get_secondary_title_setting("use_in_permalinks");
				$values = array(
					"auto"   => "",
					"custom" => "",
					"off"    => ""
				);
				foreach($values as $value => $empty) {
					if($use_permalinks_option == $value) {
						$values[$value] = ' checked="checked"';
					}
				}
			?>
			<fieldset<?php if(!$permalinks_on) {
				echo ' disabled="disabled"';
			} ?>>
				<p>
					<input type="radio" name="use_in_permalinks" id="use-in-permalinks-auto" value="auto"<?php echo $values["auto"]; ?>/>
					<label for="use-in-permalinks-auto"><?php _e("Yes, automatically append to main title.", "secondary_title"); ?></label>
				</p>
				<p>
					<input type="radio" name="use_in_permalinks" id="use-in-permalinks-custom" value="custom"<?php echo $values["custom"]; ?>/>
					<label for="use-in-permalinks-custom"><?php echo sprintf(__('Yes, use <a href="%s" title="Custom permalinks won WordPress.org" target="_blank">custom permalink structure</a>.', "secondary_title"), "http://codex.wordpress.org/Using_Permalinks#Choosing_your_permalink_structure"); ?></label>
					<?php
						$hidden = "";
						if($use_permalinks_option != "custom") {
							$hidden = ' hidden="hidden"';
						}
					?>
				<p class="description" id="use-in-permalinks-custom-description"<?php echo $hidden; ?>><?php echo sprintf(__('Use %s as a <a href="%s" title="Permalink tags on WordPress.org" target="_blank">permalink tag</a> to display the secondary title.', "secondary_title"), "<code>%secondary_title%</code>", "http://codex.wordpress.org/Using_Permalinks#Structure_Tags"); ?></p>
				<p>
					<input type="radio" name="use_in_permalinks" id="use-in-permalinks-no" value="off"<?php echo $values["off"]; ?>/>
					<label for="use-in-permalinks-no"><?php _e("Off", "secondary_title"); ?></label>
				</p>
			</fieldset>
			<p class="description">
				<?php
					echo sprintf(__('Lets you use the secondary title in <a href="%s" title="Permalinks on WordPress.org" target="_blank">permalinks</a>.', "secondary_title"), "http://codex.wordpress.org/Using_Permalinks");
					if(!$permalinks_on) {
						echo "<br /><strong>" . sprintf(__('Please <a href="%s" title="Turn on permalinks" target="_blank">turn on permalinks</a> to use this feature.', "secondary_title"), get_bloginfo("url") . "/wp-admin/options-permalink.php") . "</strong>";
					}
				?>
			</p>
		</td>
	</tr>
	</tbody>
	</table>
	<input type="hidden" name="submitted" value="true"/>
	<input type="submit" class="button button-primary" value="<?php _e("Save changes", "secondary_title"); ?>"/>
	<br/>
	<br/>
	<div id="report-bug">
		<small><?php echo sprintf(__('Found an error? Help making Secondary Title better by <a href="%s" title="Click here to report a bug" target="_blank">quickly reporting the bug</a>.', "sathon"), "http://www.wordpress.org/support/plugin/secondary-title#postform"); ?></small>
	</div>
	<?php
	}