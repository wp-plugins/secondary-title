jQuery(document).ready(function () {
	/**
	 * Inserts the secondary title input field on edit pages.
	 */
	var selector_title_input = jQuery("#secondary-title-input");
	var title_input_position = jQuery("#secondary-title-input-position").attr("value");
	if(title_input_position == "above") {
		/** Move down the "Enter title here" text displayed when the standard title field is empty to match with the input field */
		jQuery("#title-prompt-text").css("padding-top", "45px");
		selector_title_input.insertBefore("#post-body #title").css("margin-bottom", "5px").removeAttr("hidden");
	}
	if(title_input_position == "below") {
		selector_title_input.insertAfter("#post-body #title").css("margin-top", "5px").removeAttr("hidden");
	}

	/**
	 * Scripts executed on post overview page.
	 */
	if(jQuery(".edit-php").length > 0) {
		/**
		 * Add the "Sec. title" input field to the quick edit.
		 */
		jQuery("a.editinline").click(function () {
			var post_id = jQuery(this).parents("tr").attr("id").replace("post-", "");
			var secondary_title = jQuery("#post-" + post_id).find(".secondary-title-quick-edit-label").clone();
			setTimeout(function () {
				jQuery("#edit-" + post_id).find(".inline-edit-col label:first").after(secondary_title).show();
			}, 50);
		});
	}

	/**
	 * Scripts executed on plugin's settings page.
	 */
	if(jQuery("#secondary-title-settings").length > 0) {
		/** Fade out "Settings saved" message after 5 seconds */
		if(jQuery(".settings-updated").length > 0) {
			setTimeout(function () {
				jQuery(".settings-updated").fadeOut();
			}, 5000);
		}
		/** Define variables we're going to need later on */
		var preview_selector = jQuery("#title_format_preview");
		var title_format_input_selector = jQuery("#title-format-input");
		var selector_preview_title = jQuery("#preview-title").attr("value");
		var selector_preview_secondary_title = jQuery("#preview-secondary-title").attr("value");
		var preview_label = jQuery("#preview-label").attr("value");

		/**
		 * Create slide down function for long category list
		 */
		var slided = false;
		jQuery("#all_categories").click(function () {
			if(!slided) {
				jQuery("#hidden_categories").removeAttr("hidden").hide().slideDown(1000);
				jQuery("#all_categories").hide();
				slided = true;
			}
			return false;
		});

		/**
		 * "Select all" function for checkboxes
		 */
		jQuery(".select-all").click(function () {
			var selector_parent_fieldset = jQuery(this).closest("fieldset");
			jQuery(selector_parent_fieldset).find("input[type='checkbox']").each(function () {
				jQuery(this).attr("checked", "checked");
			});
			jQuery(this).hide().parents("tr").find(".unselect-all").show();
			return false;
		});

		/**
		 * "Unselect all" function for checkboxes
		 */
		jQuery(".unselect-all").click(function () {
			var selector_parent_fieldset = jQuery(this).closest("fieldset");
			jQuery(selector_parent_fieldset).find("input[type='checkbox']").each(function () {
				jQuery(this).removeAttr("checked");
			});
			jQuery(this).hide().parents("tr").find(".select-all").show();
			return false;
		});

		jQuery(".select-non-empty").click(function () {
			var checkboxes = jQuery(this).parents("fieldset").find("input[type='checkbox']");
			jQuery(checkboxes).each(function () {
				jQuery(this).removeAttr("checked");
				if(jQuery(this).parents("li").find(".count").text() >= 1) {
					jQuery(this).attr("checked", "checked");
				}
			});
			return false;
		});

		/**
		 * Displays the auto show description and enables/disabled the "only show in
		 * main posts" and title format field according to the selection.
		 */
		function ChangeAutoShow() {
			var selector_fieldset = jQuery("#auto-show-fieldset");
			var checked_radio = selector_fieldset.find("input[type='radio']:checked").attr("value");
			var description_off = selector_fieldset.find("#auto-show-off-description");
			var description_on = selector_fieldset.find("#auto-show-on-description");
			var only_show_in_main_post = jQuery("#only-show-in-main-posts-fieldset").find("input[type='radio']");
			var title_format = jQuery("#title-format-input");

			if(checked_radio == "on") {
				description_off.hide();
				description_on.fadeIn();
				title_format.removeAttr("disabled");
				only_show_in_main_post.removeAttr("disabled");
			}
			if(checked_radio == "off") {
				description_on.hide();
				description_off.fadeIn();
				title_format.attr("disabled", "disabled");
				only_show_in_main_post.attr("disabled", "disabled");
			}
		}

		/** Change when clicked */
		jQuery("#auto-show-fieldset").find("input[type='radio']").click(function () {
			ChangeAutoShow();
		});

		/**
		 * Set auto show description and "only show in main posts" +
		 * title format fields on page load
		 */
		ChangeAutoShow();

		/**
		 * Read value of the text format input and replace placeholders with random post titles.
		 *
		 * @return {boolean}
		 */
		function InsertTitlePreview() {
			var title_format_input_selector = jQuery("#title-format-input");
			/** Wait 50ms to let the entered value get ready for jQuery to get */
			setTimeout(function () {
				var preview_title = title_format_input_selector.val();
				preview_title = preview_title.replace(/%title%/g, selector_preview_title);
				preview_title = preview_title.replace(/%secondary_title%/g, selector_preview_secondary_title);
				/** Insert the preview */
				jQuery(preview_selector).html("<strong>" + preview_label + ":</strong> " + preview_title);
				if(jQuery(preview_selector).attr("hidden") == "hidden") {
					jQuery(preview_selector).removeAttr("hidden").hide().fadeIn();
				}
			}, 50);
			return true;
		}

		/**
		 * Check if text input is empty when page loads.
		 */
		if(title_format_input_selector.val() !== "") {
			/** Insert the formatted preview title */
			new InsertTitlePreview();
		}

		/**
		 * Update preview title when a new character is entered into the text input.
		 */
		title_format_input_selector.keypress(function () {
			/** Insert the formatted preview title */
			new InsertTitlePreview();
		});

		/**
		 * Resets the title format input.
		 */
		var saved_title_format = title_format_input_selector.attr("value");
		jQuery("#button-reset").click(function () {
			/** Don't do anything if title format input is disabled */
			if(title_format_input_selector.attr("disabled") == "disabled") {
				return false;
			}
			title_format_input_selector.attr("value", saved_title_format);
			new InsertTitlePreview();
			return true;
		});

		/**
		 * Adds the clicked variable to the title format input.
		 */
		jQuery("#title-format").find(".description code").click(function () {
			/** Don't insert variable if title format input is disabled */
			if(title_format_input_selector.attr("disabled") == "disabled") {
				return false;
			}
			var code_content = jQuery(this).text();
			var input_value = title_format_input_selector.attr("value");
			title_format_input_selector.attr("value", input_value + code_content);
			new InsertTitlePreview();
			return true;
		});

		jQuery("#use-in-permalinks-row").find("input").click(function () {
			var selector_permalinks_custom_description = jQuery("#use-in-permalinks-custom-description");
			var value = jQuery(this).attr("value");
			if(value == "custom") {
				selector_permalinks_custom_description.fadeIn();
			}
			else {
				selector_permalinks_custom_description.fadeOut();
			}
		});
	}
});