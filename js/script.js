$(function(){
	var required_error_text = 'This field is required to submit the form.',
		invalid_error_text 	= 'This field is invalid. Please recheck the text.',
		form_enabled = true,
		debug = true;
	
	$('form.carbon-form').submit(function(e){
		e.preventDefault();
		form_enabled = false;
		
		var form = $(this);
		var thank_you = $('#thank-you');
		
		$('.label .errors', form).remove();
		$('.label', form).removeClass('error required invalid');
		
		if(debug) console.log('Sending...');
		
		$.post(
			$(this).attr('action'),
			$(this).serialize(),
			function(data, textStatus) {
				if(debug) console.log('Received', data);
				if(data.status == 'success') {
					form.fadeTo(400, 0, function(){
						thank_you.fadeTo(400, 1);
						form.hide();
					});
				}
				else if(data.status == 'error') {
					if(data.error == 'invalid_form_data') {
						for(var e in data.errors.required) {
							var field = data.errors.required[e];
							var label = $('[name="' + field + '"]', form).parent('.label');
							label.addClass('error required').append($('<div></div>').addClass('errors').html(required_error_text));
						}
						for(var e in data.errors.invalid) {
							var field = data.errors.invalid[e];
							var label = $('[name="' + field + '"]', form).parent('.label');
							label.addClass('error invalid').append($('<div></div>').addClass('errors').html(invalid_error_text));
						}
					}
					else {
						$('.form-errors').html('<strong>' + data.error + '</strong>\n');
					}
					
					form_enabled = true;
				}
			},
			'json'
		);
	});
});
