/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

require('./bootstrap');
require('select2');
require('./bootstrap-datepicker');
require('./bootstrap-datepicker.nl-BE.min');
require('./jquery.floatThead');

var moment = require('moment');

$(function(){
    $('html').keydown(function(e){
        if ($('.btns__week').length > 0) {
            if (e.which == 37) {
                $('#btn__prev')[0].click();
            }
            if (e.which == 39) {
                $('#btn__next')[0].click();
            }
        }
    });

    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }

    // Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    });
});

$("#planning__data").floatThead({
    responsiveContainer: function($table){
        return $table.closest(".table-responsive");
    },
});

$('.js-delete').click(function(e) {
    e.preventDefault();
    $('#deleteBookingModal').modal();
    $('#deleteBooking').data('href', $(this).attr('href'));
});

$('#deleteBooking').click(() => {
    $('#deleteBookingModal').modal('hide');
    let delLocaction = $('#deleteBooking').data('href');
    window.location.href = delLocaction;
});

$("#printBtn").click(function() {
	var reinit = $("#planning__data").floatThead('destroy');
	window.print();
	reinit();
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

$('.js-extra-guest-select').select2({
	placeholder: 'Selecteer gast...',
	theme: "bootstrap",
	dropdownParent: $('#extraGuestModal'),
});

$('.js-extra-guest-select').on('select2:select', function (e) {
	var data = e.params.data;

	if(data.id == "new-guest") {
		$(".new-extra-guest").show();
	} else {
		$(".new-extra-guest").hide();
	}
});

$('.js-add-extra-guest').click((e) => {
	e.preventDefault();
	$("#extraGuestModal").modal();
	$('#addExtraGuest').prop("disabled", false);
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

$("#addExtraGuest").click(function() {
	$(this).prop("disabled", true);

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	const bookingId = $(".new-extra-guest").data('booking-id');

	let guest;

	if ($(".new-extra-guest").is(":visible")) {
		const firstname = $("[name='firstname']").val();
		const lastname = $("[name='lastname']").val();
		const email = $("[name='email']").val();
		const phone = $("[name='phone']").val();
		const country = $("[name='country']").val();

		guest = {
			'firstname': firstname,
			'lastname': lastname,
			'email': email,
			'phone': phone,
			'country': country,
		};
	} else {
		guest = {
			id: $('[name="extra-guest"]').val()
		};
	}

	$.ajax({
		url: "/planning/addExtraGuest",
		data: {
			booking: bookingId,
			guest: guest,
		},
		type: "POST",
		success: function() {
			$("#extraGuestModal").modal('hide');
			location.reload();
		},
		error: function( xhr, status, errorThrown ) {
			console.log( "Error: " + errorThrown );
			console.log( "Status: " + status );
			console.dir( xhr );
		},
	});
});

$('.booked').tooltip();

var datepicker_options = {
    format: 'dd-mm-yyyy',
    weekStart: 6,
    maxViewMode: 2,
    keyboardNavigation: false,
    autoclose: true,
    language: 'nl-BE',
    zIndexOffset: 2000,
};

// change input type first, then change date format and attach datepicker
$('[type=date]').each(function() {
    let date = moment($(this).val(), 'YYYY-MM-DD');
    $(this)
        .attr('type', 'text')
        .val(date.format('DD-MM-YYYY'));
    $(this).datepicker(datepicker_options);
});

datepicker_options.inputs = $('.actual_range');
$('.input-group.date').datepicker(datepicker_options);

$('#arrivalInput').datepicker().on('hide', function() {
    var arr_date = moment($(this).val(), 'DD-MM-YYYY');
    var dep_date = moment($('#departureInput').val(), 'DD-MM-YYYY');

    if (!dep_date.isValid() || arr_date.isSameOrAfter(dep_date)) {
        $('#departureInput').datepicker('update', arr_date.add(1,'d').format('DD-MM-YYYY'));
        $('#departureInput').removeClass('heartbeat');

        void $('#departureInput')[0].offsetWidth;
        $('#departureInput').addClass('heartbeat');
    }
});

$('#departureInput').datepicker().on('hide', function() {
    var arr_date = moment($('#arrivalInput').val(), 'DD-MM-YYYY');
    var dep_date = moment($(this).val(), 'DD-MM-YYYY');

    if (!arr_date.isValid() ||arr_date.isSameOrAfter(dep_date)) {
        $('#arrivalInput').datepicker('update', dep_date.subtract(1,'d').format('DD-MM-YYYY'));
        $('#arrivalInput').removeClass('heartbeat');

        void $('#arrivalInput')[0].offsetWidth;
        $('#arrivalInput').addClass('heartbeat');
    }
});
