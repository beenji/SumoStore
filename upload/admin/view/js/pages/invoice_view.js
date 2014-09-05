$(function() {
	$('#processPayment').click(function() {
		var form = $('#paymentForm');

		if (!form.is(':visible')) {
			form.css('opacity', 0);
			form.slideDown(200, function() {
				form.animate({opacity: 1}, 200);
			});

			return;
		}

		$.getJSON(form.attr('action') + '&' + form.serialize(), function(data) {
			if (data.success) {
				$.gritter.add({
					text: data.message,
					class_name: 'clean'
				});

				$('#amountOpen').html(data.amount);
				$('#amount').val('');

				form.animate({opacity: 0}, 200, function() {
					form.slideUp(200);
				});
			} else {
				alert(data);
			}
		});

		return false;
	})
})