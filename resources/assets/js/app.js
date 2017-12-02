
/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

require('./bootstrap');
require('select2');

var moment = require('moment');

$("#arrivalInput").change(function() {
	var arr_date = moment($(this).val());
	var dep_date = moment($("#departureInput").val());

	if (!dep_date.isValid() || arr_date.isAfter(dep_date)) {
		$("#departureInput").val(arr_date.format('YYYY-MM-DD'))
		$("#departureInput").removeClass("heartbeat");

		void $("#departureInput")[0].offsetWidth;
		$("#departureInput").addClass("heartbeat");
	}
});
$("#departureInput").change(function() {
	var arr_date = moment($("#arrivalInput").val());
	var dep_date = moment($(this).val());

	if (!arr_date.isValid() ||arr_date.isAfter(dep_date)) {
		$("#arrivalInput").val(dep_date.format('YYYY-MM-DD'));
		$("#arrivalInput").removeClass("heartbeat");

		void $("#arrivalInput")[0].offsetWidth;
		$("#arrivalInput").addClass("heartbeat");
	}
});

$('#customerSelect').select2({
	placeholder: 'Selecteer gast...',
	theme: "bootstrap"
});

$('#customerSelect').on('select2:select', function (e) {
	var data = e.params.data;
	console.log(data);

	if(data.id == "new-guest") {
		$("#newGuestModal").modal();
	}
});

$("#saveGuest").click(function() {
	$(this).prop("disabled",true);
	var firstname = $("[name='firstname']").val();
	var lastname = $("[name='lastname']").val();
	var email = $("[name='email']").val();
	var phone = $("[name='phone']").val();
	var country = $("[name='country']").val();

	var guest = {
		'firstname': firstname,
		'lastname': lastname,
		'email': email,
		'phone': phone,
		'country': country,
	};
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		// The URL for the request
		url: "/planning/saveGuest",
		// The data to send (will be converted to a query string)
		data: {
			guest: guest,
		},
		// Whether this is a POST or GET request
		type: "POST",
		// The type of data we expect back
		dataType : "json",
		// Code to run if the request succeeds;
		// the response is passed to the function
		success: function(guest) {
			console.log(guest);
			var newOption = new Option(guest.firstname + " " + guest.lastname, guest.id, false, true);
			$('#customerSelect').append(newOption).trigger('change');
			$("#newGuestModal").modal('hide');
		},
		// Code to run if the request fails; the raw request and
		// status codes are passed to the function
		error: function( xhr, status, errorThrown ) {
			console.log( "Error: " + errorThrown );
			console.log( "Status: " + status );
			console.dir( xhr );
		},
	});
});

$(".booked").tooltip();
