(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

	$( window ).load(function() {

		$(".periodic_table").click(
			function() {
				if($(this).attr('id') === 'periodic_table_clear') {
					$('.periodic_table').removeClass('included');
					$('.periodic_table').removeClass('excluded');
					setChemistryFields();
					return;
				}

				if($(this).attr('id') === 'periodic_table_all') {
					$(this).toggleClass('excluded');
					setChemistryFields();
					return;
				}

				// Check if element is "selected"
				if(!$(this).hasClass('excluded') && !$(this).hasClass('included')) {
					$(this).addClass('included')
				}
				else if($(this).hasClass('included')) {
					$(this).removeClass('included')
					$(this).addClass('excluded')
				}
				else if($(this).hasClass('excluded')) {
					$(this).removeClass('excluded')
				}

				setChemistryFields();
			}
		);

		$("#display-element-chooser").click(
			() => {
				$("#div_periodic_table").slideToggle('300')
			}
		);

		$("#rruff-search-form-submit").click(
			function() {
				// Get mineral names or RRUFF IDS from txt_mineral
				if($("#txt_mineral").val().trim().match(/^R\d{6}$/i)) {
					// display specific mineral id
					// {"dt_id":"3","34":"r040034"}
				}
				else if($("#txt_mineral").val().trim() !== '') {
					// Check for commas (separated minerals)
					// search for IMA Mineral Display Name
					// {"dt_id":"3","18":"actinolite"}
				}

				// Get General Text search field
				// {"dt_id":"3","gen":"quartz"}

				// Get chemistry includes
				// {"dt_id":"3","21":"C"}

				// Get chemistry excludes
				// {"dt_id":"3","21":"!Ni"}
				// {"dt_id":"3","21":"!Ni,!O"}

				// Get sort

				// Encode to base 64 - atob()
			}
		);
	});

	function setChemistryFields() {

		// Determine included and excluded
		let incl_val = ''
		$(".included").each(
			function() {
				let element = $(this).attr('id').replace('periodic_table_','');
				if(incl_val !== '') {
					incl_val += ", "
				}
				incl_val += element;
			}
		)
		$("#txt_chemistry_incl").val(incl_val)

		let excl_val = ''
		$(".excluded").each(
			function() {
				let element = $(this).attr('id').replace('periodic_table_','');
				if(excl_val !== '') {
					excl_val += ", "
				}
				excl_val += element;
			}
		)
		$("#txt_chemistry_excl").val(excl_val)
	}

})( jQuery );