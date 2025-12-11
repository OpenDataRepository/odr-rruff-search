let rruff_minerals = [];
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

		jQuery.when(
			// jQuery.getScript( '/odr_rruff/uploads/IMA/master_tag_data.js'),
			// jQuery.getScript( '/odr_rruff/uploads/IMA/pm_tag_data.js'),
			// Used for getting the
			jQuery.getScript('/odr_rruff/uploads/IMA/cellparams_data.js'),
			jQuery.getScript('/odr_rruff/uploads/IMA/cellparams_data_update.js'),
			jQuery.Deferred(function (deferred) {
				jQuery(deferred.resolve);
			})
		).done(() => {

			if(cellparams !== undefined) {
				for(let key of Object.keys(cellparams)) {
					// Key should also be in rruff_record_exists
					// console.log('KEY: ', key + ' ' + rruff_record_exists[key])
					if(rruff_record_exists[key] !== undefined
						&& rruff_record_exists[key] === 'true') {
						if(cellparams.hasOwnProperty(key)) {
							let cell_param_obj = cellparams[key]
							for (let mineralKey of Object.keys(cell_param_obj)){
								if(cell_param_obj.hasOwnProperty(mineralKey)) {
									let cell_param_data = cell_param_obj[mineralKey].split(/\|/)
									rruff_minerals.push(cell_param_data[2].toLowerCase())
								}
							}
						}
					}
				}
			}
			// console.log('RRUFF Minerals: ', rruff_minerals)
			rruff_minerals = rruff_minerals.filter(getUniqueValues);
			// console.log('RRUFF Minerals: ', rruff_minerals)
			rruff_minerals = localeSort(rruff_minerals)
			// console.log('RRUFF Minerals: ', rruff_minerals)


			$(".periodic_table").click(
				function () {
					if ($(this).attr('id') === 'periodic_table_clear') {
						$('.periodic_table').removeClass('included');
						$('.periodic_table').removeClass('excluded');
						setChemistryFields();
						return;
					}

					if ($(this).attr('id') === 'periodic_table_all') {
						$(this).toggleClass('excluded');
						setChemistryFields();
						return;
					}

					// Check if element is "selected"
					// !$("periodic_table_lanthanides").hasClass('included')

					if (
						$(this).hasClass("pt_lanthanides")
						&& (
							$("#periodic_table_lanthanides").hasClass('included')
							|| $("#periodic_table_lanthanides").hasClass('excluded')
						)
					) {
						return
					}
					if (
						$(this).hasClass("pt_actinides")
						&& (
							$("#periodic_table_actinides").hasClass('included')
							|| $("#periodic_table_actinides").hasClass('excluded')
						)
					) {
						return
					}

					// Deal with lanthanides & actinides
					let element = $(this).attr('id').replace('periodic_table_', '');

					if (
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
							function () {
								setInclExcl(this)
							}
						)
					} else if (
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
							function () {
								setInclExcl(this)
							}
						)
					} else if (
						element !== 'actinides'
						&& element !== 'lanthanides'
					) {
						setInclExcl(this)
					}

					setChemistryFields();
				}
			);

			$(".chemistry_lookup_link").click(
				function () {
					$("#div_periodic_table").slideToggle('100',
						function () {
							if ($("#div_periodic_table:visible") && $(window).width() < 600) {
								$('html, body').animate({
									scrollTop: ($("#div_periodic_table").offset().top - 110)
								}, 300);
							}
						})
				}
			);

			$("#reset_sample_search").click(function () {
				$("#txt_mineral").val('');
				$("#txt_general").val('');
				$("#chemistry_incl_txt").val('');
				$("#chemistry_excl_txt").val('');
				$("#txt_chemistry_incl").val('');
				$("#txt_chemistry_excl").val('');
				$("#sel_sort").val($("#sel_sort option:first").val());
				$("#sel_sort_dir").val($("#sel_sort_dir option:first").val());
				$('.periodic_table').removeClass('included');
				$('.periodic_table').removeClass('excluded');
			});

			jQuery("#rruff-search-form-wrapper input").keypress(function (e) {
				if (e.which === 13) {
					submitSearchForm();
					return false;
				}
			});

			jQuery("#rruff-search-form-submit").click(
				// Use BtoA to encode
				function () {
					submitSearchForm();
					return false;
				}
			);


			// Prepare Mineral Name Modal
			jQuery(".AMCSDMineralNameLetter").click(function () {
				rruffFilterMineralNameList(jQuery(this).html())
			});

			// TODO Add filtering for valid AMCSD records
			rruffFilterMineralNameList("A")

			jQuery(".AMCSDMineralName").click(function () {
				// If already selected, deselect and remove from list
				if (jQuery(this).hasClass('AMCSDMineralNameSelected')) {
					let mineral_name = '"' + jQuery(this).html() + '"';
					let txt_mineral = jQuery("#txt_mineral").val();
					let mineral_name_comma = mineral_name + ', ';
					if (txt_mineral.match(mineral_name_comma)) {
						jQuery('#txt_mineral').val(
							txt_mineral.replace(mineral_name_comma, '')
						)
					} else if (txt_mineral.match(mineral_name)) {
						jQuery('#txt_mineral').val(
							txt_mineral.replace(mineral_name, '')
						)
					}
					jQuery(this).removeClass('AMCSDMineralNameSelected')
				}
				else if (!jQuery(this).hasClass('AMCSDNotFound')) {
					// else select mineral
					if (jQuery("#txt_mineral").val().length === 0) {
						jQuery("#txt_mineral").val(
							'"' + jQuery(this).html() + '"'
						)
					} else {
						jQuery("#txt_mineral").val(
							jQuery("#txt_mineral").val() + ', ' +
							'"' + jQuery(this).html() + '"'
						)
					}
					jQuery(this).addClass('AMCSDMineralNameSelected')
				}
			});
		});
	});

	function submitSearchForm() {
		// UnicodeDecodeB64("JUUyJTlDJTkzJTIwJUMzJUEwJTIwbGElMjBtb2Rl"); // "✓ à la mode"
		// Get mineral names or RRUFF IDS from txt_mineral
		let search_json = {}

        if($("#txt_mineral").val().trim().match(/^R\d+$/i)) {
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
		if($("#txt_chemistry_incl").val()) {
			// {"dt_id":"3","21":"C"}
			search_json[chemistry_incl] = $("#txt_chemistry_incl").val().trim().replaceAll(/,/g,' ');
		}

		// Get chemistry excludes
		if($("#txt_chemistry_excl").val()) {
			// {"dt_id":"3","21":"!Ni"}
			// {"dt_id":"3","21":"!Ni,!O"}
			if(search_json[chemistry_incl]) {
				search_json[chemistry_incl] += ' ';
				$("#txt_chemistry_excl").val().split(/,/).forEach(
					function(item) {
						search_json[chemistry_incl] += '!' + item.trim() + ' ';
					}
				);
			}
			else {
				$("#txt_chemistry_excl").val().split(/,/).forEach(
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
        search_json.dt_id = datatype_id;
        // Get sort
        if(
            $('#sel_sort').find(':selected').val()
            && $('#sel_sort_dir').find(':selected').val()
        ) {
            search_json['sort_by'] = []
            search_json['sort_by'][0] = { };
            search_json['sort_by'][0]['sort_df_id'] = $('#sel_sort').find(':selected').val();
            search_json['sort_by'][0]['sort_dir'] = $('#sel_sort_dir').find(':selected').val();
        }

        // console.log("SJ", search_json);

        // alert(JSON.stringify(search_json));return false;

        // Encode to base 64 - atob()
	    let search_string = b64EncodeUnicode(JSON.stringify(search_json)); // "JUUyJTlDJTkzJTIwJUMzJUEwJTIwbGElMjBtb2Rl"
		search_string = search_string.replace(/==$/, '');
		search_string = search_string.replace(/=$/, '');
		// https://beta.rruff.net/odr/rruff_samples#/odr/search/display/7/eyJkdF9pZCI6IjMifQ/1
		if(redirect_url === '/odr/network') {
			window.location = redirect_url, true
		}
		else {
			let redirect =  redirect_url + "/" + search_string;
			window.location = redirect, true
		}
	}

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

function rruffClearMineralNameList() {
    // Clear minerals_selected array
    minerals_selected = [];
    jQuery(".AMCSDMineralNameLetter").removeClass('AMCSDAlphaSelected');

    jQuery(".AMCSDMineralName").each(function () {
        jQuery(this).removeClass('AMCSDMineralNameSelected')
        jQuery("#txt_mineral").val('');
    })
}

function rruffFilterMineralNameList(letter) {
	jQuery(".AMCSDMineralName").hide()
	jQuery(".AMCSDMineralName").removeClass("AMCSDNotFound")

	let regex = new RegExp('^' + letter, 'i');
	let mineral_list_objects = jQuery(".AMCSDMineralName");
	for(let item of mineral_list_objects) {
		// Add check if mineral name is in list from cellparams data
		let mineral_name = jQuery(item).html().toLowerCase();
		if (mineral_name.substring(0,1).localeCompare(letter.toLowerCase(), 'en', {sensitivity: "base"}) > 0) {
			break;
		}
		if (mineral_name.match(regex)) {
			// HiLoSearch to find mineral....??
			jQuery(item).show()
			if(!hiLoSearch(mineral_name, rruff_minerals)) {
				// fade minerals with no AMCSD record
				jQuery(item).addClass('AMCSDNotFound')
			}
		}
	}
}



function b64EncodeUnicode(str) {
	// first we use encodeURIComponent to get percent-encoded Unicode,
	// then we convert the percent encodings into raw bytes which
	// can be fed into btoa.
	return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
		function toSolidBytes(match, p1) {
			return String.fromCharCode('0x' + p1);
		}))
		.replace(/\+/g, '-')
		.replace(/\//g, '_')
		.replace(/=+$/, '');
}


function UnicodeDecodeB64(str) {
	return decodeURIComponent(
		atob(
			str.replace(/-/g, '+')
				.replace(/_/g, '/')
				.padEnd(value.length + (m === 0 ? 0 : 4 - m), '=')
		)
	);
}



function localeSort(array_obj) {
	array_obj.sort( (a,b) => {
		let nameA = a.toLowerCase().replace(/[\(\)\-\_]/g, '');
		let nameB = b.toLowerCase().replace(/[\(\)\-\_]/g, '');
		return nameA.localeCompare(nameB, 'en')
	});

	return array_obj;
}


function getUniqueValues(value, index, array) {
	return array.indexOf(value) === index;
}


function hiLoSearch(search_string, array_obj) {
	// max 10 loops = 2^10
	let low = 0;
	let high = array_obj.length - 1;
	let mid = Math.floor((high-low)/2)
	for(let i= 0; i < 30; i++) {
		let value = array_obj[mid];
		if (search_string.localeCompare(value, 'en', {sensitivity: "base"}) === 0) {
			return value;
		}
		else if (search_string.localeCompare(value, 'en', {sensitivity: "base"}) < 0) {
			// Before
			high = mid
			mid = Math.floor(high - (high-low)/2)
		}
		else {
			// After
			low = mid
			mid = Math.floor(low + (high-low)/2)
		}
	}
	return null
}
