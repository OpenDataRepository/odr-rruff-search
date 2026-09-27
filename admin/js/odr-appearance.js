/**
 * Appearance section of the ODR search plugin settings: color pickers and
 * corner radius inputs that update a live preview of the search form.
 */
(function ($) {
	'use strict';

	$(function () {
		var $section = $('#odr-appearance');
		if (!$section.length) return;
		var preview = $section.find('.odr-appearance-preview')[0];

		function setVar(name, value) {
			if (preview && name) preview.style.setProperty(name, value);
		}

		$section.find('.odr-appearance-color').each(function () {
			var $input = $(this);
			$input.wpColorPicker({
				change: function (event, ui) {
					setVar($input.attr('data-var'), ui.color.toString());
				},
				// "Clear" in the picker falls back to the default color
				clear: function () {
					setVar($input.attr('data-var'), $input.attr('data-default-color'));
				}
			});
		});

		function setRadius($number, value) {
			var max = parseInt($number.attr('max'), 10);
			var px = Math.max(0, Math.min(max, parseInt(value, 10) || 0));
			$number.val(px);
			$section.find('input[type=range][data-for="' + $number.attr('id') + '"]').val(px);
			setVar($number.attr('data-var'), px + 'px');
		}

		$section.on('input change', '.odr-appearance-number', function () {
			if (this.value !== '') setRadius($(this), this.value);
		});
		$section.on('input', 'input[type=range]', function () {
			setRadius($('#' + $(this).attr('data-for')), this.value);
		});

		$section.on('click', '.odr-appearance-reset', function () {
			$section.find('.odr-appearance-color').each(function () {
				var $input = $(this);
				$input.wpColorPicker('color', $input.attr('data-default-color'));
				setVar($input.attr('data-var'), $input.attr('data-default-color'));
			});
			$section.find('.odr-appearance-number').each(function () {
				setRadius($(this), $(this).attr('data-default'));
			});
		});
	});
})(jQuery);
