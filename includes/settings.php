<?php
	/*
	 * This file handles everything within the "Settings" > "Secondary Title"
	 * settings page within the admin area.
	 *
	 * @package       Secondary Title
	 * @subpackage    Administration
	 */

	/** Send me an e-mail when a bug report is filed and sent. */
	if(isset($_GET["report_bug"]) && $_GET["report_bug"] == "true") {
		/** Define headers */
		$headers = "From: " . strip_tags($_GET["email"]) . "\r\n";
		$headers .= "Reply-To: " . strip_tags($_GET["email"]) . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$message = '<html><body><strong>E-Mail:</strong> ' . $_GET["email"] . '<br /><br />' . $_GET["bug_description"] . '</body></html>';
		/** Send the actual e-mail */
		mail("kolja.nolte@gmail.com", "Bug Report: Secondary Title", $_GET["bug_description"], $headers);
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
			$posts = new WP_Query(array(
				"showposts" => 1,
				"meta_key"  => "_secondary_title"
			));
			/** If there are no posts with a secondary title, use static text */
			if(!$posts->have_posts()) {
				$preview_title           = __("Plane missing", "secondary_title");
				$preview_secondary_title = __("Malaysian Airlines flight MH370 lost over Gulf of Thailand", "secondary_title");
			}
			else {
				$random_number  = rand(0, count($posts->posts) - 1);
				$random_post_id = $posts->posts[$random_number]->ID;

				$preview_title           = $posts->posts[$random_number]->post_title;
				$preview_secondary_title = get_secondary_title($random_post_id);
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
								$filtered_post_types = get_filtered_post_types();
								$post_types          = get_secondary_title_post_types();
								$checked             = "";
								$counter             = 0;
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
									|
									<a href="#" title="<?php _e("Unselect all", "secondary_title"); ?>" class="unselect-all"><?php _e("Unselect all", "secondary_title"); ?></a>
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
								$categories         = get_terms("category", array(
									"hide_empty" => false
								));
								$counter            = 0;
								$categories_counter = count($categories);
								foreach($categories as $category) {
									/** Checks selected categories */
									$checked             = "";
									$selected_categories = get_option("secondary_title_categories");
									if(is_array($selected_categories) && in_array($category->slug, $selected_categories)) {
										$checked = " checked";
									}
									if($counter == 10) {
										echo '<div id="hidden_categories" hidden="hidden">';
									}
									?>
									<input type="checkbox" name="categories[<?php echo $counter; ?>]" id="category-<?php echo $category->slug; ?>" value="<?php echo $category->slug; ?>"<?php echo $checked; ?> />
									<label for="category-<?php echo $category->slug; ?>"><?php echo $category->name; ?></label>
									<br/>
									<?php
									if($counter > 10 && $counter == $categories_counter - 1) {
										echo '</div>';
									}
									$counter++;
								}
								if($categories_counter > 10) {
									echo '<a href="#" id="all_categories" title="' . __("Click here to see all available categories", "secondary_title") . '">' . __("Show all categories", "secondary_title") . '</a>';
								}
							?>
							<p>
								<small>
									<a href="#" title="<?php _e("Select all", "secondary_title"); ?>" class="select-all"><?php _e("Select all", "secondary_title"); ?></a>
									|
									<a href="#" title="<?php _e("Unselect all", "secondary_title"); ?>" class="unselect-all"><?php _e("Unselect all", "secondary_title"); ?></a>
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
						<label for="auto_show_on"><?php _e("Insert automatically", "secondary_title"); ?></label>
					</th>
					<td>
						<fieldset>
							<input type="radio" name="auto_show" id="auto_show_on" value="on"<?php if(get_option("secondary_title_auto_show") == "on" || get_option("secondary_title_auto_show") == "") {
								echo " checked";
							} ?> />
							<label for="auto_show_on"><?php _e("Yes", "secondary_title"); ?></label>

							<input type="radio" name="auto_show" id="auto_show_off" value="off"<?php if(get_option("secondary_title_auto_show") == "off") {
								echo " checked";
							} ?> />
							<label id="label_auto_show_off" for="auto_show_off"><?php _e("No", "secondary_title"); ?></label>
							<?php
								$readme_url     = plugin_dir_url(__FILE__) . "../readme.txt";
								$auto_show_info = sprintf(__('To manually insert the secondary title in your theme, use <code>&lt;?php get_secondary_title(); ?&gt;</code><br />or <code>&lt;?php the_secondary_title(); ?&gt;</code>. See <a href="%s" title="Open readme.txt">readme.txt</a> or the <a href="%s" title="See official documentation" target="_blank" >official documentation</a> for additional parameters.', "secondary_title"), $readme_url, "http://www.koljanolte.com/wordpress/plugins/secondary-title/");
							?>
							<p id="auto_show_functions" class="description" hidden="hidden"><?php echo $auto_show_info; ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="title_format"><?php _e("Title format", "secondary_title"); ?></label>
					</th>
					<td>
						<input type="text" name="title_format" id="title_format" class="regular-text" placeholder="<?php _e("E.g.: %secondary_title%: %title%", "secondary_title"); ?>" value="<?php echo stripslashes(str_replace('"', "'", get_option("secondary_title_title_format"))); ?>" autocomplete="off"/>

						<p class="description"><?php _e("Use <code>%title%</code> for the main title and <code>%secondary_title%</code> for the secondary title.", "secondary_title"); ?></p>

						<p class="description" id="title_format_preview" hidden="hidden"></p><br/>

						<p class="description">
							<?php
								echo sprintf(__('<b>Note:</b> To style the output, use the <a href="%s" title="See an explanation on w3schools.com" target="_blank">style HTML attribute</a>, e.g.:<br /><code>%s</code>', "secondary_title"), "http://www.w3schools.com/tags/att_global_style.asp", esc_attr('<span style="color:red;font-size:14px;">%secondary_title%</span>'));
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

						<p class="description"><?php echo sprintf(__('Defines whether input field for the secondary title should be displayed above or below<br />the standard title <strong>within the add/edit post/page area</strong> on the admin interface.<br />See the <a href="%s" title="See the FAQ" target="_blank">FAQ</a> if you want to apply the same effect on your front end.', "secondary_title"), "http://www.koljanolte.com/wordpress/plugins/secondary-title/#FAQ"); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row" id="only_show_in_main_post">
						<label for="only_show_in_main_post_yes"><?php _e("Only show in main post", "secondary_title"); ?></label>
					</th>
					<td>
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
					</td>
				</tr>
			</tbody>
		</table>
		<p><?php echo sprintf(__('Do you speak more than one language? Help making Secondary Title<br />easier to use for foreign users by <a href="%s" title="Go to project page on Transifex.com" target="_blank">translating the plugin on Transifex.</a>', "secondary_title"), "https://www.transifex.com/projects/p/plugin-secondary-title/"); ?></p>
		<div id="report-bug">
			<input type="hidden" id="report-bug-file-path" value="<?php echo plugin_dir_url(__FILE__); ?>settings.php"/>
			<small><?php echo sprintf(__('Found an error? Help making Secondary Title better by <a href="#" title="Click here to report a bug">quickly reporting the bug</a>.', "sathon")); ?></small>
			<div id="report-bug-loading-icon" hidden="hidden">
				<br/>
				<br/>
				<img src="<?php echo plugin_dir_url(__FILE__) . "../images/loading.gif"; ?>" alt="<?php _e("Loading", "secondary_title"); ?>..."/>
			</div>
			<div id="bug-form" hidden="hidden">
				<br/>
				<textarea name="bug_description" id="bug-form-textarea" cols="53" rows="5" placeholder="<?php _e("In a few words, please explain the bug and when exactly it occurs...", "secondary_title"); ?>"></textarea>
				<br/>
				<input type="email" placeholder="<?php _e("Your e-mail address in case there're questions...", "sathon"); ?>"/>
				<input type="submit"
					value="<?php _e("Report bug", "sathon"); ?>" class="button"/>
			</div>
			<div id="bug-form-error" hidden="hidden">
				<p><?php _e("Please fill out both, the bug description and the e-mail field<br /> so I can get back to you if I have any questions regarding that bug.", "secondary_title"); ?></p>
			</div>
			<div id="bug-form-response" hidden="hidden">
				<p><?php _e("Thanks for reporting the bug. I'll try my best to fix it<br />and include the fix in the new version.", "sathon"); ?></p>
			</div>
		</div>
		<p class="submit">
			<input type="hidden" name="submitted" value="1"/>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e("Save Changes", "secondary_title"); ?>"/>
		</p>
		</form>
		</div>
	<?php
	}

	/**
	 * When post is being saved, update post's post meta with the new secondary title.
	 *
	 * @since 0.1
	 */
	function init_secondary_title_admin_save() {
		if(!empty($_POST["secondary_post_title"])) {
			update_post_meta(get_the_ID(), "_secondary_title", $_POST["secondary_post_title"]);
		}
	}

	add_action("edit_post", "init_secondary_title_admin_save");