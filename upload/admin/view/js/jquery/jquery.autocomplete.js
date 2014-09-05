(function($) {
	$.fn.autocomplete = function(options) {
		var settings = $.extend({
			callback: null,
			param: 'q',
			url: '',
			labelKey: '',
			extraParams: {},
			minLength: 3
		}, options);

		return this.each(function() {
			// Do stuff
			var elem = $(this);
				elem.prop('autocomplete', 'off');
				elem.wrap('<div>');

			elem.on('keydown', function() {
				var val = elem.val();

				if (val == elem.data('selected-option') || val == '') {
					return;
				}

				elem.parent().css('position', 'relative');
				var ac = $('<ul class="autocomplete" />');

				ac.css('top', (elem.parent().height() - 1));
				ac.css('min-width', 200);

				settings.extraParams[settings.param] = val;
				var queryString = $.param(settings.extraParams);

				$.getJSON(settings.url + '?' + queryString, function(result) {

					// Remove old autocomplete
					$('ul.autocomplete').remove();

					$.each(result, function(label, data) {
						var inputLabel = '';

						if (data[settings.labelKey] != undefined) {
							if (label.isNaN) {
								label = label + ' (' + data[settings.labelKey] + ')';
							} else {
								label = data[settings.labelKey];
							}
							inputLabel = data[settings.labelKey];
						} else {
							inputLabel = label;
						}

						var li = $('<li />');
						var a = $('<a />').text(label);

						a.click(function() {
							//elem.data('product-id', productData['id']);
							elem.val(inputLabel);
							elem.data('selected-option', inputLabel);

							if (typeof(settings.callback) == 'function') {
								settings.callback.call(this, elem, data);
							}

							ac.remove();
						})

						a.appendTo(li);
						li.appendTo(ac);
					});

					elem.after(ac);
				});
			})

			elem.on('blur', function() {
				if (elem.val() != elem.data('selected-option')) {
					elem.val('');
				}

				setTimeout(function() { $('ul.autocomplete').remove() }, 200);
			})
		})
	}
})(jQuery);