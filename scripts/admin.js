/**
 * Checks whether the current viewed page is the
 * plugin's settings page.
 *
 * @return {boolean}
 */
function IsOnSettingsPage() {
	var is = false;
	if(jQuery("#secondary-title-settings").length) {
		is = true;
	}
	return is;
}

/**
 * Checks whether the current viewed page is the
 * post/page/custom post type edit page.
 *
 * @returns {boolean}
 * @constructor
 */
function IsOnEditPage() {
	var is = false;
	if(jQuery("#post-body").length) {
		is = true;
	}
	return is;
}

/**
 * @constructor
 */
function MoveQuickEdit() {
	var container = jQuery(event.currentTarget).parents("tr");
	var post_id = jQuery(container).attr("id").replace("post-", "");
	setTimeout(function() {
		var edit = jQuery("#edit-" + post_id);
		var secondary_title = jQuery("#post-" + post_id).find(".column-secondary_title .row-title").text();
		var secondary_title_container = edit.find(".quick_edit_secondary_title_input").parents("label");
		secondary_title_container.insertAfter("#edit-" + post_id + " .inline-edit-col label:first");

		edit.find(".quick_edit_secondary_title_input").attr("value", secondary_title);
	}, 100);
}

jQuery(document).ready(function() {
	/**
	 * Inserts value and changes position of the secondary title input field
	 * in the quick edit section on posts/pages overview.
	 */
	jQuery(".editinline").click(function() {
		new MoveQuickEdit();
	});

	/**
	 * The functions for the settings page.
	 */
	if(IsOnEditPage()) {
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
	}
	if(IsOnSettingsPage()) {
		/** Define variables we're going to need */
		var preview_selector = jQuery("#title_format_preview");
		var title_format_input_selector = jQuery("#title-format-input");
		var selector_preview_title = jQuery("#preview-title").attr("value");
		var selector_preview_secondary_title = jQuery("#preview-secondary-title").attr("value");
		var preview_label = jQuery("#preview-label").attr("value");

		/**
		 * Create slide down function for long category list
		 */
		var slided = false;
		jQuery("#all_categories").click(function() {
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
		jQuery(".select-all").click(function() {
			var selector_parent_fieldset = jQuery(this).closest("fieldset");
			jQuery(selector_parent_fieldset).find("input[type='checkbox']").each(function() {
				jQuery(this).attr("checked", "checked");
			});
			return false;
		});

		/**
		 * "Unselect all" function for checkboxes
		 */
		jQuery(".unselect-all").click(function() {
			var selector_parent_fieldset = jQuery(this).closest("fieldset");
			jQuery(selector_parent_fieldset).find("input[type='checkbox']").each(function() {
				jQuery(this).removeAttr("checked");
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
		jQuery("#auto-show-fieldset").find("input[type='radio']").click(function() {
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
			setTimeout(function() {
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
		title_format_input_selector.keypress(function() {
			/** Insert the formatted preview title */
			new InsertTitlePreview();
		});

		/**
		 * Resets the title format input.
		 */
		var saved_title_format = title_format_input_selector.attr("value");
		jQuery("#button-reset").click(function() {
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
		jQuery("#title-format").find(".description code").click(function() {
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

		jQuery("#use-in-permalinks-row").find("input").click(function() {
			var selector_permalinks_custom_description = jQuery("#use-in-permalinks-custom-description");
			var value = jQuery(this).attr("value");
			if(value == "custom") {
				selector_permalinks_custom_description.fadeIn();
			}
			else {
				selector_permalinks_custom_description.hide();
			}
		});

		/**
		 * "Report bug" function.
		 */
		var settings_file_path = jQuery("#report-bug-file-path").attr("value");
		var selector_report_bug = jQuery("#report-bug");
		selector_report_bug.find("a").click(function() {
			jQuery("#bug-form").slideToggle();
			return false;
		});
		selector_report_bug.find("#submit-bug-report").click(function() {
			var selector_bug_form = jQuery("#bug-form");
			/** Check if all fields have been filled out, otherwise display the error message */
			var required_fields = [
				"textarea", "input[type='email']"
			];
			var has_empty_fields = false;
			jQuery(selector_bug_form).find("" + required_fields + "").each(function() {
				var this_field = jQuery(this);
				if(this_field.val() == "") {
					this_field.css("background-color", "#FFFFC4");
					has_empty_fields = true;
				}
				else {
					this_field.css("background-color", "#FFFFFF");
				}
			});
			if(has_empty_fields) {
				jQuery("#bug-form-response-empty-fields").fadeIn();
				return false;
			}
			else {
				jQuery("#bug-form-response-empty-fields").hide();
			}
			var selector_loading_icon = jQuery("#report-bug-loading-icon");
			var bug_description = selector_bug_form.find("textarea").val();
			var user_email = selector_bug_form.find("input[type=email]").val();
			selector_bug_form.html(selector_loading_icon.show());
			/** Send form data to settings.php for processing in PHP */
			jQuery.ajax({
				type:    "get",
				url: settings_file_path + "?action=rate&report_bug=true&description=" + bug_description + "&email=" + user_email,
				success: function() {
					selector_loading_icon.hide();
					jQuery("#bug-form-response-success").fadeIn();
				},
				error:   function() {
					selector_loading_icon.hide();
					jQuery("#bug-form-response-error").fadeIn();
				}
			});
			return false;
		});
	}
});