<?php
	/**
	 * Stop script when the file is called directly.
	 */
	if(!function_exists("add_action")) {
		return false;
	}

	/**
	 * Load the text domain for localization. blabla
	 */
	function init_languages() {
		load_plugin_textdomain("secondary_title", false, dirname(plugin_basename(__FILE__)) . "/languages/");
	}

	add_action("init", "init_languages");
	/**
	 * Get selected post IDs.
	 *
	 * @return array|mixed|void Post IDs
	 */
	function get_secondary_title_post_ids() {
		$post_ids = get_option("secondary_title_post_ids");
		if(!is_array($post_ids)) {
			$post_ids = array();
		}
		return $post_ids;
	}

	/**
	 * Get selected post types.
	 *
	 * @return array|mixed|void Post types
	 */
	function get_secondary_title_post_types() {
		$post_types = get_option("secondary_title_post_types");
		if(!is_array($post_types)) {
			$post_types = array();
		}
		return $post_types;
	}

	/**
	 * Get selected categories.
	 *
	 * @return array|mixed|void Selected categories
	 */
	function get_secondary_title_post_categories() {
		$post_categories = get_option("secondary_title_categories");
		if(!is_array($post_categories)) {
			$post_categories = array();
		}
		return $post_categories;
	}

	/**
	 * Get the secondary title from post ID $post_id
	 *
	 * @param int $post_id ID of target post
	 *
	 * @return mixed The secondary title
	 *
	 */
	function get_secondary_title($post_id = 0) {
		/** If $post_id not set, use current post ID */
		if(!$post_id) {
			$post_id = get_the_ID();
		}
		$post_ids = get_secondary_title_post_ids();
		if(count($post_ids) != 0 && !in_array($post_id, $post_ids)) {
			return false;
		}
		/** Return the secondary title */
		return get_post_meta($post_id, "_secondary_title", true);
	}

	/**
	 * Prints the secondary title and adds an optional suffix.
	 *
	 * @param int    $post_id ID of target post
	 * @param string $suffix  To be added after the secondary title
	 */
	function the_secondary_title($post_id = 0, $suffix = "") {
		echo get_secondary_title($post_id) . $suffix;
	}

	/**
	 * Return all available post types except pages, attachments, revision ans nav_menu_items.
	 *
	 * @return array
	 */
	function get_filtered_post_types() {
		/** Gets all registered post types */
		$post_types = get_post_types();
		$output     = array();
		foreach($post_types as $post_type) {
			/** Filters out useless post types  */
			if($post_type != "attachment" && $post_type != "revision" && $post_type != "nav_menu_item") {
				/** Saves the remaining post types in $output */
				array_push($output, $post_type);
			}
		}
		return $output;
	}

	/**
	 * If auto show function is set, replace the post titles with custom title format.
	 *
	 * @param $title
	 *
	 * @return mixed
	 */
	function secondary_title_auto_show($title) {
		global $post;
		$post_category = get_the_category();
		$post_category = $post_category[0]->slug;
		/** Checks if auto show function is set and the secondary title is not empty */
		if(get_option("secondary_title_auto_show") == "on" && get_secondary_title() != "" && $title == $post->post_title || is_admin() && get_secondary_title() != "") {
			$post_ids        = get_secondary_title_post_ids();
			$post_types      = get_secondary_title_post_types();
			$post_categories = get_secondary_title_post_categories();
			/** Stop script if it does not match the set options */
			if(count($post_ids) != 0 && !in_array(get_the_ID(), $post_ids) || count($post_types) != 0 && !in_array(get_post_type(), $post_types) || count($post_categories) != 0 && !in_array($post_category, $post_categories)) {
			}
			else {
				/** Apply title format */
				$format = get_option("secondary_title_title_format");
				$title  = str_replace("%title%", $title, $format);
				$title  = str_replace("%secondary_title%", get_secondary_title(), $title);
			}
		}
		return $title;
	}

	add_filter("the_title", "secondary_title_auto_show", 10, 2);

	/**
	 * Register and load the plugin's stylesheet.
	 */
	function admin_load_css() {
		wp_register_style("secondary-title", plugin_dir_url(__FILE__) . "style.css", array());
		wp_enqueue_style("secondary-title");
	}

	add_action("admin_enqueue_scripts", "admin_load_css");

	/**
	 * Initialize secondary title within the admin interface.
	 *
	 * @return bool
	 */
	function init_admin_posts() {
		$current_screen = get_current_screen();
		/** Don't insert the text input when not viewing the post or edit page */
		if($current_screen->base != "post" && $current_screen->base != "edit") {
			return false;
		}
		$post_types = get_option("secondary_title_post_types");
		$post_ids   = get_option("secondary_title_post_ids");
		if(is_array($post_types) || is_array($post_ids)) {
			/** Stop script when post_type is not among the set post types, same with post IDs */
			if(count($post_types) != 0 && !in_array(get_post_type(), $post_types) || count($post_ids) != 0 && !in_array(get_the_ID(), $post_ids)) {
				return false;
			}
		}
		/** Insert text input on edit post page via jQuery */
		?>
		<style type="text/css">
			#secondary-title-input {
				margin-bottom: 5px;
			}

			#secondary-title-text {
				width:  100%;
				height: 30px;
			}
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#secondary-title-input").insertBefore("#title").removeAttr("hidden");
			});
		</script>
		<div id="secondary-title-input" hidden="hidden">
			<label for="secondary-title-text" hidden="hidden"></label>
			<input type="text" size="30" id="secondary-title-text" name="secondary_post_title" value="<?php the_secondary_title(); ?>" />
		</div>
		<?php
		return true;
	}

	add_action("admin_head", "init_admin_posts");

	/**
	 * When post is being saved, update post's post meta with the new secondary title.
	 */
	function init_admin_save() {
		update_post_meta(get_the_ID(), "_secondary_title", esc_attr($_POST["secondary_post_title"]));
	}

	add_action("edit_post", "init_admin_save");

	/**
	 * Initialize setting on admin interface.
	 */
	function init_admin_settings() {
		/** Creates a new page on the admin interface */
		add_options_page(__("Secondary Title settings"), "Secondary Title", "manage_options", "secondary_title", "build_admin_settings");
	}

	add_action("admin_menu", "init_admin_settings");

	/**
	 * Build the option page.
	 */
	function build_admin_settings() {
		/** Check if the submit button was hit */
		if(isset($_POST["submitted"])) {
			print_r($_POST["categories"]);
			/** If no post type checked, turn the variable into an array */
			if(!isset($_POST["post_types"])) {
				update_option("secondary_title_post_types", array());
			}
			/** If no categories checked, turn the variable into an array */
			if(!isset($_POST["categories"])) {
				update_option("secondary_title_categories", array());
			}
			/** Gets all input fields */
			foreach($_POST as $key => $value) {
				if($key == "post_ids") {
					if(empty($value)) {
						$value = array();
					}
					else {
						/** Turn the string into an array with single post IDs */
						$value = str_replace(" ", "", $value);
						$value = explode(",", $value);
					}
				}
				/** Filter out unnecessary fields and update option */
				if($key != "submit" || $key != "publication" && $key != "post_types" || $key != "categories") {
					update_option("secondary_title_" . $key, $value);
				}
			}
			/** Displays message when settings are saved */
			echo '<div id="message" class="updated"><p><strong>' . __("Settings saved.", "default") . '</strong></p></div>';
		}
		/**
		 * Build the jQuery scripts and the actual settings page.
		 */
		?>
		<div class="wrap">
		<h2><?php echo __("Settings", "default") . " › " . __("Secondary Title", "secondary_title"); ?></h2>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var slided = false;
				jQuery("#all_categories").click(function() {
					if(!slided) {
						jQuery("#hidden_categories").removeAttr("hidden").hide().slideDown(1000);
						jQuery("#all_categories").hide();
						slided = true;
					}
					return false;
				});
				var checked = false;
				var auto_show_off = "#auto_show_off";
				/** Hide the title format input when clicked */
				if(jQuery(auto_show_off).is(":checked")) {
					jQuery("#title_format").attr("disabled", "disabled");
					jQuery("#auto_show_functions").removeAttr("hidden");
					checked = true;
				}
				/** Activate the title format input when clicked */
				jQuery("#auto_show_on").click(function() {
					jQuery("#title_format").removeAttr("disabled");
					if(checked) {
						jQuery("#auto_show_functions").hide();
						checked = false;
					}
				});
				jQuery(auto_show_off).click(function() {
					jQuery("#title_format").attr("disabled", "disabled");
					if(!checked) {
						jQuery("#auto_show_functions").removeAttr("hidden").hide().fadeIn();
						checked = true;
					}
				});
				<?php
					/** Get a random post with a secondary title */
					$posts = new WP_Query(array("showposts" => 1, "meta_key" => "_secondary_title"));
					/** If there are no posts with a secondary title, use static text */
					if(!$posts->have_posts()) {
						$random_title           = __("I love this plugin", "secondary_title");
						$random_secondary_title = __("Important news", "secondary_title");
					}
					else {
						$random_number          = rand(0, count($posts->posts)-1);
						$random_post_id         = $posts->posts[$random_number]->ID;
						$random_title           = $posts->posts[$random_number]->post_title;
						$random_secondary_title = get_secondary_title($random_post_id);
					}
				?>
				var preview_selector = "#title_format_preview";
				var title_format_selector = "#title_format";

				/**
				 * Read value of the text format input and replace placeholders with random post titles.
				 *
				 * @constructor
				 */
				function InsertTitlePreview() {
					var preview_title = jQuery(title_format_selector).val();
					preview_title = preview_title.replace("%title%", "<?php echo $random_title; ?>");
					preview_title = preview_title.replace("%secondary_title%", "<?php echo $random_secondary_title; ?>");
					jQuery(preview_selector).html("<b><?php _e("Preview", "default"); ?>:</b> " + preview_title);
					if(jQuery(preview_selector).attr("hidden") == "hidden") {
						jQuery(preview_selector).removeAttr("hidden").hide().fadeIn();
					}
				}

				/**
				 * Check if text input is empty when page loads.
				 */
				if(jQuery(title_format_selector).val() !== "") {
					/** Insert the formatted preview title */
					new InsertTitlePreview();
				}

				/**
				 * Update preview title when a new character is entered into the text input.
				 */
				jQuery(title_format_selector).keypress(function() {
					/** Insert the formatted preview title */
					new InsertTitlePreview();
				});
			});
		</script>
		<form method="post" action="">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="post_types"><?php _e("Post types", "secondary_title"); ?></label>
						</th>
						<td>
							<fieldset>
								<?php
									$filtered_post_types = get_filtered_post_types();
									$post_types = get_secondary_title_post_types();
									$checked = "";
									$counter = 0;
									foreach($filtered_post_types as $post_type) {
										$post_type = get_post_type_object($post_type);
										if(is_array($post_types)) {
											if(in_array($post_type->name, $post_types)) {
												$checked = " checked";
											}
										}
										?>
										<input type="checkbox" name="post_types[]" id="<?php echo $post_type->name; ?>" value="<?php echo $post_type->name; ?>"<?php echo $checked;
											$checked = ""; ?> />
										<label for="<?php echo $post_type->name; ?>"><?php echo $post_type->labels->name; ?></label>
										<br />
										<?php
										$counter++;
									}
								?>
								<p class="description"><?php _e("Post types for which secondary titles should be activated.<br /> Select none to use all available post types.", "secondary_title"); ?></p>
							</fieldset>
						</td>
					<tr>
						<th scope="row">
							<label for="categories"><?php _e("Categories", "default"); ?></label>
						</th>
						<td>
							<fieldset>
								<?php
									/** Show also empty categories */
									$categories = get_terms("category", array(
											"hide_empty" => false
									));
									$counter = 0;
									$categories_counter = count($categories);
									foreach($categories as $category) {
										$checked             = "";
										$selected_categories = get_option("secondary_title_categories");
										if(is_array($selected_categories) && in_array($category->slug, $selected_categories)) {
											$checked = " checked";
										}
										if($counter == 10) {
											echo '<div id="hidden_categories" hidden>';
										}
										?>
										<input type="checkbox" name="categories[<?php echo $counter; ?>]" id="<?php echo $category->slug; ?>" value="<?php echo $category->slug; ?>"<?php echo $checked; ?> />
										<label for="<?php echo $category->slug; ?>"><?php echo $category->name; ?></label>
										<br />
										<?php
										if($counter > 10 && $counter == $categories_counter - 1) {
											echo '</div>';
										}
										$counter++;
									}
									if($categories_counter > 10) {
										echo '<a href="#" id="all_categories">' . __("Show all categories", "secondary_title") . '</a>';
									}
								?>
								<p class="description"><?php _e("Categories for which secondary titles should be activated.<br /> Select none to use all available categories.", "secondary_title"); ?></p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="post_ids"><?php _e("Post IDs", "secondary_title"); ?></label>
						</th>
						<td>
							<input type="text" id="post_ids" class="regular-text" name="post_ids" placeholder="<?php _e("E.g.: 4, 28, 104", "secondary_title"); ?>" value="<?php echo implode(", ", get_secondary_title_post_ids()); ?>" />
							<br />

							<p class="description"><?php _e("Only use secondary title for specific posts. Separate IDs with commas.", "secondary_title"); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="auto_show_on"><?php _e("Insert automatically", "secondary_title"); ?></label>
						</th>
						<td>
							<fieldset>
								<input type="radio" name="auto_show" id="auto_show_on" value="on"<?php if(get_option("secondary_title_auto_show") == "on" || get_option("secondary_title_auto_show") == "") {
									echo " checked";
								} ?> />
								<label for="auto_show_on"><?php _e("Yes", "default"); ?></label>

								<input type="radio" name="auto_show" id="auto_show_off" value="off"<?php if(get_option("secondary_title_auto_show") == "off") {
									echo " checked";
								} ?> />
								<label id="label_auto_show_off" for="auto_show_off"><?php _e("No", "default"); ?></label>

								<p id="auto_show_functions" class="description" hidden="hidden"><?php _e('To manually insert the secondary title in your theme, use <code>&lt;?php get_secondary_title(); ?&gt;</code><br />or <code>&lt;?php the_secondary_title(); ?&gt;</code>. See readme.txt for additional parameters.', "secondary_title"); ?></p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="title_format"><?php _e("Title format", "secondary_title"); ?></label>
						</th>
						<td>
							<input type="text" name="title_format" id="title_format" class="regular-text" placeholder="<?php _e("E.g.: %secondary_title%: %title%", "secondary_title"); ?>" value="<?php echo get_option("secondary_title_title_format"); ?>" autocomplete="off" />

							<p class="description"><?php _e("Use <code>%title%</code> for the main title and <code>%secondary_title%</code> for the secondary title.", "secondary_title"); ?></p>

							<p class="description" id="title_format_preview" hidden="hidden"></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="hidden" name="submitted" value="1" />
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e("Save Changes", "default"); ?>" />
			</p>
		</form>
		</div>
	<?php
	}

?>