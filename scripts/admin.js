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
 * Inserts value and changes position of the secondary title input field
 * in the quick edit section on posts/pages overview.
 */
jQuery(".editinline").click(function() {
	var post_id = jQuery(this).parents("tr").attr("id");
	post_id = post_id.replace("post-", "");
	var secondary_title = jQuery("#post-" + post_id + " .secondary_title").text();

	/** Wait a bit to let the quick edit box load */
	setTimeout(function() {
		var quick_edit_secondary_title_input = jQuery("#edit-" + post_id + " .quick_edit_secondary_title_input");
		quick_edit_secondary_title_input.attr("value", secondary_title);
		jQuery(quick_edit_secondary_title_input).parents("label").insertAfter("#edit-" + post_id + " label:first");
	}, 100);
});

/**
 * The functions for the settings page.
 */
jQuery(document).ready(function() {
	/**
	 * Inserts the secondary title input field on edit pages.
	 */
	if(IsOnEditPage()) {
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

		jQuery(".unselect-all").click(function() {
			var selector_parent_fieldset = jQuery(this).closest("fieldset");
			jQuery(selector_parent_fieldset).find("input[type='checkbox']").each(function() {
				jQuery(this).removeAttr("checked");
			});
			return false;
		});

		/**
		 * Deactivates "Only show in main post" setting
		 * since it is only effective when "Insert automatically" is on.
		 *
		 * @type {boolean}
		 */
		var checked = false;
		var auto_show_off = "#auto_show_off";
		if(jQuery(auto_show_off).is(":checked")) {
			jQuery("#title_format").attr("disabled", "disabled");
			jQuery("#only_show_in_main_post_yes").attr("disabled", "disabled");
			jQuery("#only_show_in_main_post_no").attr("disabled", "disabled");
			jQuery("#auto_show_functions").removeAttr("hidden");
			checked = true;
		}
		/** Activate the title format input when clicked */
		jQuery("#auto_show_on").click(function() {
			jQuery("#title_format").removeAttr("disabled");
			jQuery("#only_show_in_main_post_yes").removeAttr("disabled");
			jQuery("#only_show_in_main_post_no").removeAttr("disabled");
			if(checked) {
				jQuery("#auto_show_functions").hide();
				checked = false;
			}
		});
		/** Disable title format input field when auto title is active */
		jQuery(auto_show_off).click(function() {
			jQuery("#title_format").attr("disabled", "disabled");
			jQuery("#only_show_in_main_post_yes").attr("disabled", "disabled");
			jQuery("#only_show_in_main_post_no").attr("disabled", "disabled");
			if(!checked) {
				jQuery("#auto_show_functions").removeAttr("hidden").hide().fadeIn();
				checked = true;
			}
		});

		var preview_selector = "#title_format_preview";
		var title_format_selector = "#title_format";
		var selector_preview_title = jQuery("#preview-title").attr("value");
		var selector_preview_secondary_title = jQuery("#preview-secondary-title").attr("value");
		var preview_label = jQuery("#preview-label").attr("value");

		/**
		 * Read value of the text format input and replace placeholders with random post titles.
		 *
		 * @return {boolean}
		 */
		function InsertTitlePreview() {
			var preview_title = jQuery(title_format_selector).val();
			preview_title = preview_title.replace("%title%", selector_preview_title);
			preview_title = preview_title.replace("%secondary_title%", selector_preview_secondary_title);
			jQuery(preview_selector).html("<b>" + preview_label + ":</b> " + preview_title);
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

		/**
		 * "Report bug" function
		 */
		var settings_file_path = jQuery("#report-bug-file-path").attr("value");
		var selector_report_bug = jQuery("#report-bug");
		var selector_bug_form = jQuery("#bug-form");

		selector_report_bug.find("a").click(function() {
			jQuery("#bug-form").slideToggle();
			return false;
		});

		selector_report_bug.find("input[type=submit]").click(function() {
			/** Check if all fields have been filled out, otherwise display the error message */
			if(selector_bug_form.find("textarea").val() == "" || selector_bug_form.find("input").val() == "") {
				jQuery("#bug-form-error").fadeIn();
				return false;
			}
			else {
				jQuery("#bug-form-error").hide();
			}

			jQuery("#bug-form-response").fadeIn();
			selector_bug_form.html(jQuery("#report-bug-loading-icon").show());
			selector_bug_form.load(settings_file_path + "?report_bug=true", function() {
				jQuery("#report-bug-loading-icon").hide();
			});
			return false;
		});
	}
});