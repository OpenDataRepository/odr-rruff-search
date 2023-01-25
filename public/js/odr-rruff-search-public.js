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
				// !$("periodic_table_lanthanides").hasClass('included')

				if(
					$(this).hasClass("pt_lanthanides")
					&& (
						$("#periodic_table_lanthanides").hasClass('included')
						|| $("#periodic_table_lanthanides").hasClass('excluded')
					)
				) {
					return
				}
				if(
					$(this).hasClass("pt_actinides")
					&& (
						$("#periodic_table_actinides").hasClass('included')
						|| $("#periodic_table_actinides").hasClass('excluded')
					)
				) {
					return
				}

				// Deal with lanthanides & actinides
				let element = $(this).attr('id').replace('periodic_table_','');

				if(
					element === 'lanthanides'
					&& (
						(
							!$(this).hasClass("included")
							&& !$(this).hasClass("excluded")
							&& !$(".pt_lanthanides").hasClass('included') // .length === 0
							&& !$(".pt_lanthanides").hasClass('excluded') // .length === 0
						)
						|| (
							$(this).hasClass("included")
							&& $(".pt_lanthanides").hasClass('included')
						)
						|| (
							$(this).hasClass("excluded")
							&& $(".pt_lanthanides").hasClass('excluded')
						)
					)
				) {
					setInclExcl(this)
					$(".pt_lanthanides").each(
						function() {
							setInclExcl(this)
						}
					)
				}
				else if(
					element === 'actinides'
					&& (
						(
							!$(this).hasClass("included")
							&& !$(this).hasClass("excluded")
							&& !$(".pt_actinides").hasClass('included') // .length === 0
							&& !$(".pt_actinides").hasClass('excluded') // .length === 0
						)
						|| (
							$(this).hasClass("included")
							&& $(".pt_actinides").hasClass('included')
						)
						|| (
							$(this).hasClass("excluded")
							&& $(".pt_actinides").hasClass('excluded')
						)
					)
				) {
					setInclExcl(this)
					$(".pt_actinides").each(
						function() {
							setInclExcl(this)
						}
					)
				}
				else if (
					element !== 'actinides'
					&& element !== 'lanthanides'
				) {
					setInclExcl(this)
				}

				setChemistryFields();
			}
		);

		$(".chemistry_lookup_link").click(
			function() {
				$("#div_periodic_table").slideToggle('300',
					function() {
						if($("#div_periodic_table:visible") && $(window).width() < 600) {
							$('html, body').animate({
								scrollTop: ($("#div_periodic_table").offset().top - 110)
							}, 2000);
						}
					})
			}
		);

		$("#reset_sample_search").click(function() {
				window.location = '/odr/network';
		});


		$("#rruff-search-form-submit").click(
			// Use BtoA to encode

			function() {
				// UnicodeDecodeB64("JUUyJTlDJTkzJTIwJUMzJUEwJTIwbGElMjBtb2Rl"); // "✓ à la mode"
				// Get mineral names or RRUFF IDS from txt_mineral
				let search_json = {
					"dt_id": datatype_id
				};
				if($("#txt_mineral").val().trim().match(/^R\d{6}$/i)) {
					// display specific mineral id
					// {"dt_id":"3","34":"r040034"}
					search_json[sample_id] = $("#txt_mineral").val().trim();
				}
				else if($("#txt_mineral").val().trim() !== '') {
					// Check for commas (separated minerals)
					// search for IMA Mineral Display Name
					// {"dt_id":"3","18":"actinolite"}
					search_json[mineral_name] = $("#txt_mineral").val().trim();
				}

				// Get General Text search field
				if($("#txt_general").val().trim() !== '') {
					// {"dt_id":"3","gen":"quartz"}
					search_json[general_search] = $("#txt_general").val().trim();
				}

				// Get chemistry includes
				if($("#chemistry_incl_txt").val()) {
					// {"dt_id":"3","21":"C"}
					search_json[chemistry_incl] = $("#chemistry_incl_txt").val().trim().replace(/,/,' ');
				}

				// Get chemistry excludes
				if($("#chemistry_excl_txt").val()) {
					// {"dt_id":"3","21":"!Ni"}
					// {"dt_id":"3","21":"!Ni,!O"}
					if(search_json[chemistry_incl]) {
						search_json[chemistry_incl] += ' ';
						$("#chemistry_excl_txt").val().split(/,/).forEach(
							function(item) {
								search_json[chemistry_incl] += '!' + item.trim() + ' ';
							}
						);
					}
					else {
						$("#chemistry_excl_txt").val().split(/,/).forEach(
							function(item) {
								search_json[chemistry_incl] += '!' + item.trim() + ' ';
							}
						);
					}
				}

				/*
					$criteria['sort_by'] = array(
                    	'sort_dir' => $sort_dir,
                    	'sort_df_id' => $sort_df_id
                	);
				 */

				// Get sort
				if(
					$('#sel_sort').find(':selected').val()
					&& $('#sel_sort_dir').find(':selected').val()
				) {
					search_json['sort_by'] = { };
					search_json['sort_by']['sort_df_id'] = $('#sel_sort').find(':selected').val();
					search_json['sort_by']['sort_dir'] = $('#sel_sort_dir').find(':selected').val();
				}

				// Encode to base 64 - atob()
			    let search_string = b64EncodeUnicode(JSON.stringify(search_json)); // "JUUyJTlDJTkzJTIwJUMzJUEwJTIwbGElMjBtb2Rl"
				search_string = search_string.replace(/==$/, '');
				search_string = search_string.replace(/=$/, '');
				// https://beta.rruff.net/odr/rruff_samples#/odr/search/display/7/eyJkdF9pZCI6IjMifQ/1
				if(redirect_url === '/odr/network') {
					window.location = redirect_url, true
				}
				else {
					let redirect =  redirect_url + "/" + search_string + "/1";
					window.location = redirect, true
				}
			}
		);
	});

	function setInclExcl(obj) {
		// Check if element is "selected"
		if(!$(obj).hasClass('excluded') && !$(obj).hasClass('included')) {
			$(obj).addClass('included')
		}
		else if($(obj).hasClass('included')) {
			$(obj).removeClass('included')
			$(obj).addClass('excluded')
		}
		else if($(obj).hasClass('excluded')) {
			$(obj).removeClass('excluded')
		}
	}

	function setChemistryFields() {

			// Determine included and excluded
		let incl_val = ''
		let txt_incl_val = ''
		$(".included").each(
			function() {
				let element = $(this).attr('id').replace('periodic_table_','');
				if(
					element !== "lanthanides"
					&& element !== "actinides"
				) {
					if(incl_val !== '') {
						incl_val += ", "
					}
					incl_val += element;
				}
				if(
					!$(this).hasClass('pt_lanthanides')
					&& !$(this).hasClass('pt_actinides')
				) {
					if (txt_incl_val !== '') {
						txt_incl_val += ", "
					}
					txt_incl_val += element;
				}
				else if(
					$(this).hasClass('pt_lanthanides')
					&& !$("#periodic_table_lanthanides").hasClass('included')
					&& !$("#periodic_table_lanthanides").hasClass('excluded')
				) {
					if (txt_incl_val !== '') {
						txt_incl_val += ", "
					}
					txt_incl_val += element;
				}
				else if(
					$(this).hasClass('pt_actinides')
					&& !$("#periodic_table_actinides").hasClass('included')
					&& !$("#periodic_table_actinides").hasClass('excluded')
				) {
					if (txt_incl_val !== '') {
						txt_incl_val += ", "
					}
					txt_incl_val += element;
				}
			}
		)
		$("#chemistry_incl_txt").val(incl_val)
		$("#txt_chemistry_incl").val(txt_incl_val)

		let excl_val = ''
		let txt_excl_val = ''
		$(".excluded").each(
			function() {
				let element = $(this).attr('id').replace('periodic_table_', '');
				if(
					element !== "lanthanides"
					&& element !== "actinides"
				) {
					if (excl_val !== '') {
						excl_val += ", "
					}
					excl_val += element;
				}
				if(
					!$(this).hasClass('pt_lanthanides')
					&& !$(this).hasClass('pt_actinides')
				) {
					if (txt_excl_val !== '') {
						txt_excl_val += ", "
					}
					txt_excl_val += element;
				}
				else if(
					$(this).hasClass('pt_lanthanides')
					&& !$("#periodic_table_lanthanides").hasClass('included')
					&& !$("#periodic_table_lanthanides").hasClass('excluded')
				) {
					if (txt_excl_val !== '') {
						txt_excl_val += ", "
					}
					txt_excl_val += element;
				}
				else if(
					$(this).hasClass('pt_actinides')
					&& !$("#periodic_table_actinides").hasClass('included')
					&& !$("#periodic_table_actinides").hasClass('excluded')
				) {
					if (txt_excl_val !== '') {
						txt_excl_val += ", "
					}
					txt_excl_val += element;
				}
			}
		)
		$("#chemistry_excl_txt").val(excl_val)
		$("#txt_chemistry_excl").val(txt_excl_val)
	}

})( jQuery );

function b64EncodeUnicode(str) {
	return btoa(str);
}

function UnicodeDecodeB64(str) {
	return decodeURIComponent(atob(str));
}
