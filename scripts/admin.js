jQuery(document).ready(
	function() {
		var previewSelector, titleFormatInputSelector, selectorPreviewTitle, selectorPreviewSecondaryTitle, previewLabel, slided, savedTitleFormat, inputValue, codeContent, value, selectorPermalinkDescription;

		/**
		 * Inserts the secondary title input field on edit pages.
		 */
		var selectorTitleInput = jQuery("#secondary-title-input");
		var titleInputPosition = jQuery("#secondary-title-input-position").attr("value");
		if(titleInputPosition === "above") {
			/** Move down the "Enter title here" text displayed when the standard title field is empty to match with the input field */
			jQuery("#title-prompt-text").css("padding-top", "45px");
			selectorTitleInput.insertBefore("#post-body #title").css("margin-bottom", "5px").removeAttr("hidden");
		}
		if(titleInputPosition === "below") {
			selectorTitleInput.insertAfter("#post-body #title").css("margin-top", "5px").removeAttr("hidden");
		}

		/**
		 * Scripts executed on post overview page.
		 */
		if(jQuery(".edit-php").length > 0) {
			/**
			 * Add the "Sec. title" input field to the quick edit.
			 */
			jQuery("a.editinline").click(
				function() {
					var postId = jQuery(this).parents("tr").attr("id").replace("post-", "");
					var secondaryTitle = jQuery(".secondary-title-quick-edit-label").clone();
					setTimeout(
						function() {
							jQuery("#edit-" + postId).find(".inline-edit-col label:first").after(secondaryTitle).show();
						}, 50
					);
				}
			);
		}

		/**
		 * Scripts executed on plugin's settings page.
		 */
		if(jQuery("#secondary-title-settings").length > 0) {
			/** Fade out "Settings saved" message after 5 seconds */
			if(jQuery(".settings-updated").length > 0) {
				setTimeout(
					function() {
						jQuery(".settings-updated").fadeOut();
					}, 5000
				);
			}
			/** Define variables we're going to need later on */
			previewSelector = jQuery("#title_format_preview");
			titleFormatInputSelector = jQuery("#title-format-input");
			selectorPreviewTitle = jQuery("#preview-title").attr("value");
			selectorPreviewSecondaryTitle = jQuery("#preview-secondary-title").attr("value");
			previewLabel = jQuery("#preview-label").attr("value");

			/**
			 * Create slide down function for long category list
			 */
			slided = false;
			jQuery("#all_categories").click(
				function() {
					if(!slided) {
						jQuery("#hidden_categories").removeAttr("hidden").hide().slideDown(1000);
						jQuery("#all_categories").hide();
						slided = true;
					}
					return false;
				}
			);

			/**
			 * "Select all" function for checkboxes
			 */
			jQuery(".select-all").click(
				function() {
					var selectorParentFieldset = jQuery(this).closest("fieldset");
					jQuery(selectorParentFieldset).find("input[type='checkbox']").each(
						function() {
							jQuery(this).attr("checked", "checked");
						}
					);
					jQuery(this).hide().parents("tr").find(".unselect-all").show();
					return false;
				}
			);

			/**
			 * "Unselect all" function for checkboxes
			 */
			jQuery(".unselect-all").click(
				function() {
					var selectorParentFieldset = jQuery(this).closest("fieldset");
					jQuery(selectorParentFieldset).find("input[type='checkbox']").each(
						function() {
							jQuery(this).removeAttr("checked");
						}
					);
					jQuery(this).hide().parents("tr").find(".select-all").show();
					return false;
				}
			);

			jQuery(".select-non-empty").click(
				function() {
					var checkboxes = jQuery(this).parents("fieldset").find("input[type='checkbox']");
					jQuery(checkboxes).each(
						function() {
							jQuery(this).removeAttr("checked");
							if(jQuery(this).parents("li").find(".count").text() >= 1) {
								jQuery(this).attr("checked", "checked");
							}
						}
					);
					return false;
				}
			);

			/**
			 * Displays the auto show description and enables/disabled the "only show in
			 * main posts" and title format field according to the selection.
			 */
			function changeAutoShow() {
				var selectorFieldset = jQuery("#auto-show-fieldset");
				var checkedRadio = selectorFieldset.find("input[type='radio']:checked").attr("value");
				var descriptionOff = selectorFieldset.find("#auto-show-off-description");
				var descriptionOn = selectorFieldset.find("#auto-show-on-description");
				var onlyShowInMainPost = jQuery("#only-show-in-main-posts-fieldset").find("input[type='radio']");
				var titleFormat = jQuery("#title-format-input");

				if(checkedRadio === "on") {
					descriptionOff.hide();
					descriptionOn.fadeIn();
					titleFormat.removeAttr("disabled");
					onlyShowInMainPost.removeAttr("disabled");
				}
				if(checkedRadio === "off") {
					descriptionOn.hide();
					descriptionOff.fadeIn();
					titleFormat.attr("disabled", "disabled");
					onlyShowInMainPost.attr("disabled", "disabled");
				}
			}

			/** Change when clicked */
			jQuery("#auto-show-fieldset").find("input[type='radio']").click(
				function() {
					changeAutoShow();
				}
			);

			/**
			 * Set auto show description and "only show in main posts" +
			 * title format fields on page load
			 */
			changeAutoShow();

			/**
			 * Read value of the text format input and replace placeholders with random post titles.
			 *
			 * @return {boolean}
			 */
			function insertTitlePreview() {
				var titleFormatInputSelector = jQuery("#title-format-input");
				/** Wait 50ms to let the entered value get ready for jQuery to get */
				setTimeout(
					function() {
						var previewTitle = titleFormatInputSelector.val();
						previewTitle = previewTitle.replace(/%title%/g, selectorPreviewTitle);
						previewTitle = previewTitle.replace(/%secondary_title%/g, selectorPreviewSecondaryTitle);
						/** Insert the preview */
						jQuery(previewSelector).html("<strong>" + previewLabel + ":</strong> " + previewTitle);
						if(jQuery(previewSelector).attr("hidden") === "hidden") {
							jQuery(previewSelector).removeAttr("hidden").hide().fadeIn();
						}
					}, 50
				);
				return true;
			}

			/**
			 * Check if text input is empty when page loads.
			 */
			if(titleFormatInputSelector.val() !== "") {
				/** Insert the formatted preview title */
				insertTitlePreview();
			}

			/**
			 * Update preview title when a new character is entered into the text input.
			 */
			titleFormatInputSelector.keypress(
				function() {
					/** Insert the formatted preview title */
					insertTitlePreview();
				}
			);

			/**
			 * Resets the title format input.
			 */
			savedTitleFormat = titleFormatInputSelector.attr("value");
			jQuery("#button-reset").click(
				function() {
					/** Don't do anything if title format input is disabled */
					if(titleFormatInputSelector.attr("disabled") === "disabled") {
						return false;
					}
					titleFormatInputSelector.attr("value", savedTitleFormat);
					insertTitlePreview();
					return true;
				}
			);

			/**
			 * Adds the clicked variable to the title format input.
			 */
			jQuery("#title-format").find(".description code").click(
				function() {
					/** Don't insert variable if title format input is disabled */
					if(titleFormatInputSelector.attr("disabled") === "disabled") {
						return false;
					}
					codeContent = jQuery(this).text();
					inputValue = titleFormatInputSelector.attr("value");
					titleFormatInputSelector.attr("value", inputValue + codeContent);
					insertTitlePreview();
					return true;
				}
			);

			jQuery("#use-in-permalinks-row").find("input").click(
				function() {
					selectorPermalinkDescription = jQuery("#use-in-permalinks-custom-description");
					value = jQuery(this).attr("value");
					if(value === "custom") {
						selectorPermalinkDescription.fadeIn();
					} else {
						selectorPermalinkDescription.fadeOut();
					}
				}
			);
		}
	}
);