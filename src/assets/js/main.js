jQuery(document).ready(function($){
	$.noConflict();	

 	$.fn.extend({
	  	HBForms: function(config) {
	    	self.init(this, config)
	  	} 
	});

	$.validator.setDefaults({
	    errorElement: 'span',
	    errorPlacement: function (error, element) {
	        error.addClass('invalid-feedback');
	        element.closest('.form-group').append(error);
	    },
	    highlight: function (element, errorClass, validClass) {
	        $(element).addClass('is-invalid');
	    },
	    unhighlight: function (element, errorClass, validClass) {
	        $(element).removeClass('is-invalid');
	    },
	    onfocusout: function(element) {
	        this.element(element);  
	    },
	    submitHandler: function(form) {
	    	self.clearAlert(form);
	    	self.handleAjax(form);
    	} 
	});

	$('[data-type=date]').each( function(index, elem) {
		$(elem).datepicker({
			dateFormat: $(elem).data('format') ? $(elem).data('format') : 'dd/mm/yy',
			maxDate: $(elem).data('maxdate') ? $(elem).data('maxdate') : '',
			minDate: $(elem).data('mindate') ? $(elem).data('mindate') : '',
			onClose: function () {
		        $(this).focusout();
		    }
		});
	});

	$.validator.addMethod("date", function(value, element, dateFormat) {
        return  moment(value, dateFormat.toUpperCase(), true).isValid();
    }, HBF.validation_messages.date );

    $.validator.addMethod("sameTo", function(value, element, arg) {
    	return value == $('input[name='+arg+']').val();
    }, HBF.validation_messages.sameTo );

	var self =   {
		init: function(form, config) {
			self.props = config;
			$(".hb-ajax-form").each(this.handleOnSubmit)
	  	},

	  	handleOnSubmit: function(index, elem) {
	  		let formId = $(elem).attr('id');
		  	$(elem).validate({ rules:  window[formId.toUpperCase()].rules  });
	  	},

	  	handleAjax: function(form) {
	  		$.ajax({
	  			type: 'POST',
	  			data: $(form).serialize(),
				url: HBF.endpoint,
				dataType: "JSON", 
				success: function (response) {
					self.handleSuccess(response, form)
				},
				error: function(error) {
					self.handleErrors(error, form)
				}
	  		});
	  	},

	  	clearAlert: function( form ) {
	  		$(form).find('[role=alert]').remove();
	  	},

	  	handleSuccess: function(response, form) {
	  		response = response.data;

	  		if( response.error == false && response.message != '' ) {
				$(form).prepend(`<div class="${ self.props.successAlertClass }" role="alert">${response.message}</div>`);
			}
 
			document.getElementById($(form).attr('id')).dispatchEvent(new CustomEvent("onHBFormSuccess", { detail: response }));

			$(form)[0].reset();
	  	},

	  	handleErrors: function(error, form) {
	  		let response = JSON.parse(error.responseText).data,
				validator = $(form).validate();

	  		if( response.error == true && typeof response.data == 'object' ) {
				$.each(response.data, function( index, value ) {
					let input_error = [];
					input_error[ index ] = value;
					validator.showErrors(input_error);
				});
			}	

			if( response.error == true && response.message != '' ) {
				$(form).prepend(`<div class="${ self.props.errorAlertClass }" role="alert">${response.message}</div>`);
			}

			document.getElementById($(form).attr('id')).dispatchEvent(new CustomEvent("onHBFormError", { detail: response }));
	  	}
	}

	$(".hb-ajax-form").HBForms({
		errorAlertClass: 'alert alert-danger',
		successAlertClass: 'alert alert-success'
	});

});  	