(function ($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	jQuery(function ($) {

		jQuery("#disable_payment_methods").selectWoo();

		function enableDisable() {
			jQuery(document).on('click', '.pi-dpmw-status-change', function (e) {
				var id = jQuery(this).data('id');
				var status = jQuery(this).is(":checked") ? 1 : 0;
				jQuery("#pisol-dpmw-disable-rules-list-view").addClass('blocktable');
				jQuery.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						id: id,
						status: status,
						action: 'pisol_dpmw_change_status',
						_wpnonce: dpmw_variables._wpnonce
					}
				}).always(function (d) {
					jQuery("#pisol-dpmw-disable-rules-list-view").removeClass('blocktable');
				})
			});
		}
		enableDisable();

		jQuery('body').on('focus', '.time-picker', function () {
			if(jQuery.fn.timepicker != undefined){
				var obj = jQuery(this).timepicker({
					interval: 5,
					scrollbar: true,
					dynamic: false
				});
			}else{
				jQuery(this).attr('placeholder', 'HH:mm');
				jQuery(this).attr('readonly', false);
			}
		});

		jQuery(document).on('click', '.pi-clear-time', function () {
			jQuery(this).parent().children('input').val("");
		});

		jQuery("body").on("focus", ".date-picker", function () {
			if(jQuery.fn.flatpickr != undefined){
				jQuery(this).flatpickr({
					dateFormat: "Y/m/d"
				});
			}else{
				jQuery(this).attr('placeholder', 'yyyy/mm/dd');
				jQuery(this).attr('readonly', false);
			}
		});

		jQuery("body").on("focus", ".multi-date-picker", function () {
			if(jQuery.fn.flatpickr != undefined){
				jQuery(this).flatpickr({
					mode: "multiple",
					dateFormat: "Y/m/d"
				});
			}else{
				jQuery(this).attr('placeholder', 'yyyy/mm/dd, yyyy/mm/dd, yyyy/mm/dd');
				jQuery(this).attr('readonly', false);
			}
		});

		function ajaxSubmit() {
			$('#pisol-dpmw-new-method').submit(function (e) {
				e.preventDefault();
				var form = $(this);
				blockUI()
				$.ajax({
					type: "POST",
					url: ajaxurl,
					dataType: 'json',
					data: form.serialize(), // serializes the form's elements.
					success: function (data) {


						if (data.error != undefined) {
							var html = ''

							jQuery.each(data.error, function (index, val) {
								html += '<p class="pi-dpmw-notice error">' + val + '<span class="pi-close-notification dashicons dashicons-no-alt"></span></p>';
							});

							jQuery("#pisol-dpmw-notices").html(html);

							$.alert({
								title: 'Error',
								content: html,
								type: 'red',
								columnClass: 'small'
							});
						}

						if (data.success != undefined) {
							var html = '<p class="pi-dpmw-notice success">' + data.success + '<span class="pi-close-notification dashicons dashicons-no-alt"></span></p>';
							jQuery("#pisol-dpmw-notices").html(html);

							$.alert({
								title: 'Success',
								content: html,
								type: 'green',
								columnClass: 'small'
							});
						}

						if (data.redirect != undefined) {
							window.location = data.redirect;
						}
					}
				}).always(function () {
					unblockUI();
				});
			});
		}
		ajaxSubmit();

		function blockUI() {
			jQuery("#pisol-dpmw-new-method").addClass('pi-blocked')
		}

		function unblockUI() {
			jQuery("#pisol-dpmw-new-method").removeClass('pi-blocked')
		}

		function hideNotification() {
			jQuery(document).on('click', '.pi-close-notification', function () {
				jQuery(this).parent().remove();
			})
		}
		hideNotification();

		function confirmDelete() {
			jQuery(document).on('click', ".pisol-confirm", function (e) {
				var choice = confirm("Are you sure you want to delete it ?");
				if (!choice) {
					e.preventDefault();
				}
			});
		}
		confirmDelete();


		function ruleType() {
			jQuery(document).on('change', "#pi_rule_type", function () {
				var type = jQuery(this).val();

				titleShow(type);
			});
			jQuery("#pi_rule_type").trigger('change');
		}
		ruleType();

		function titleShow(type) {
			jQuery('.pi-rule-type').each(function () {
				var stored_type = jQuery(this).data('type');
				if (stored_type == type) {
					jQuery(this).fadeIn();
				} else {
					jQuery(this).fadeOut();
				}
			})
		}

		jQuery("#pi_dpmw_remove_payment_methods, #pi_dpmw_remove_payment_methods_selected, #pi_currency").selectWoo();

	});

})(jQuery);
