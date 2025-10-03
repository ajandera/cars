<? echo "/*";?><script type="text/javascript"><? echo "*/";?>



$.datepicker.regional['cs'] = {
        closeText: 'Zavřít',
        prevText: '&#x3c;Dříve',
        nextText: 'Později&#x3e;',
        currentText: 'Nyní',
        monthNames: ['leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen',
            'září', 'říjen', 'listopad', 'prosinec'],
        monthNamesShort: ['leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec'],
        dayNames: ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'],
        dayNamesShort: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],
        dayNamesMin: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],
        weekHeader: 'Týd',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };

 $.datepicker.regional['es'] = {
	closeText: "Cerrar",
	prevText: "Ant",
	nextText: "Sig",
	currentText: "Hoy",
	monthNames: [ "enero", "febrero", "marzo", "abril", "mayo", "junio",
	"julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre" ],
	monthNamesShort: [ "ene", "feb", "mar", "abr", "may", "jun",
	"jul", "ago", "sep", "oct", "nov", "dic" ],
	dayNames: [ "domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado" ],
	dayNamesShort: [ "dom", "lun", "mar", "mié", "jue", "vie", "sáb" ],
	dayNamesMin: [ "D", "L", "M", "X", "J", "V", "S" ],
	weekHeader: "Sm",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: ""
	};

 $.datepicker.regional['hu'] = {
	closeText: "Bezár",
	prevText: "Vissza",
	nextText: "Előre",
	currentText: "Ma",
	monthNames: [ "Január", "Február", "Március", "Április", "Május", "Június",
	"Július", "Augusztus", "Szeptember", "Október", "November", "December" ],
	monthNamesShort: [ "Jan", "Feb", "Már", "Ápr", "Máj", "Jún",
	"Júl", "Aug", "Szep", "Okt", "Nov", "Dec" ],
	dayNames: [ "Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat" ],
	dayNamesShort: [ "Vas", "Hét", "Ked", "Sze", "Csü", "Pén", "Szo" ],
	dayNamesMin: [ "V", "H", "K", "Sze", "Cs", "P", "Szo" ],
	weekHeader: "Hét",
	dateFormat: "yy.mm.dd.",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: "" };



 $.datepicker.regional['pl'] = {
	closeText: "Zamknij",
	prevText: "Poprzedni",
	nextText: "Następny",
	currentText: "Dziś",
	monthNames: [ "Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec",
	"Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień" ],
	monthNamesShort: [ "Sty", "Lu", "Mar", "Kw", "Maj", "Cze",
	"Lip", "Sie", "Wrz", "Pa", "Lis", "Gru" ],
	dayNames: [ "Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota" ],
	dayNamesShort: [ "Nie", "Pn", "Wt", "Śr", "Czw", "Pt", "So" ],
	dayNamesMin: [ "N", "Pn", "Wt", "Śr", "Cz", "Pt", "So" ],
	weekHeader: "Tydz",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };




	$.datepicker.setDefaults($.datepicker.regional['<?php echo (!empty($_GET['language'])?$_GET['language']:'cs');?>']);



var stopForm = 0;

/*
* jQuery UI Autocomplete HTML Extension
*
* Copyright 2010, Scott González (http://scottgonzalez.com)
* Dual licensed under the MIT or GPL Version 2 licenses.
*
* http://github.com/scottgonzalez/jquery-ui-extensions
*/
(function( $ ) {

var proto = $.ui.autocomplete.prototype,
initSource = proto._initSource;

function filter( array, term ) {
var matcher = new RegExp( $.ui.autocomplete.escapeRegex(term), "i" );
return $.grep( array, function(value) {
return matcher.test( $( "<div>" ).html( value.label || value.value || value ).text() );
});
}

$.extend( proto, {
_initSource: function() {
if ( this.options.html && $.isArray(this.options.source) ) {
this.source = function( request, response ) {
response( filter( this.options.source, request.term ) );
};
} else {
initSource.call( this );
}
},

_renderItem: function( ul, item) {
return $( "<li></li>" )
.data( "item.autocomplete", item )
.append( $( "<a></a>" )[ this.options.html ? "html" : "text" ]( item.label ) )
.appendTo( ul );
}
});

})( jQuery );


$(function() {

	$('.tooltips,.napoveda').tooltip();
	//$('.napoveda').popover({title:"Nápověda",placement: 'auto top'});
	//$('.info').popover({title:"Informace",placement: 'auto top'})

	/*$("#searchIcon").click(function(){

		$("#searchForm").fadeToggle();
		//$("#searchInput").focus();

	});*/
	$(".subRoll .subRollClick").click(function()
	{
		var idParent = $(this).parent().attr('id');
		$("#c"+idParent).slideToggle();
	});

	$("#filtrForm input").change(function(){

		$("#filtrForm").submit();

	});
	/*$("#searchForm").submit(function(){
		//$("#searchInput").focus();
		return false;
	});*/

	$("#idSearchForm").submit(function()
	{
		var id = $('#idSearch').val();
		location.href='/zastavy/admin.zastavy-nahled/id/'+id+'/';
		return false;
	});

	$("#idSearchForm2").submit(function()
	{
		var id = $('#idSearch2').val();
		location.href='/zakaznici/admin.zakaznici-nahled/'+id+'/';
		return false;
	});


	$('#modalVymahani').on('show.bs.modal', function (event)
	{
		var button = $(event.relatedTarget);
		var label = button.data('label');
		var type = button.data('type');
		var id = button.data('id');
		var modal = $(this);

		modal.find('#modalVymahaniLabel').html(label);
		if(type=="dovymahani")
		{
			modal.find('.modal-body').html('<iframe src="/vymahani/object.ajax.dovymahani/'+id+'" class="" style="border:0" width="100%" border="0" height="500">');
		}
		else if(type=="doagentury")
		{
			modal.find('.modal-body').html('<iframe src="/vymahani/object.ajax.doagentury/'+id+'" class="" style="border:0" width="100%" border="0" height="500">');
		}
	});
});

jQuery.validator.addMethod("multiemail", function (value, element) {
    if (this.optional(element)) {
        return true;
    }

    var emails = value.split(','),
        valid = true;

    for (var i = 0, limit = emails.length; i < limit; i++) {
        value = emails[i];
        valid = valid && jQuery.validator.methods.email.call(this, value, element);
    }

    return valid;
}, "<?php echo l("Špatný formát emailu: na rozdělení více emailů použijte čárku.")?>");


$.validator.addMethod( "dic", function( value, element ) {
	return this.optional( element ) || /^((AT)U[0-9]{8}|(BE)0[0-9]{9}|(BG)[0-9]{9,10}|(CY)[0-9]{8}L|(CZ)[0-9]{8,10}|(DE)[0-9]{9}|(DK)[0-9]{8}|(EE)[0-9]{9}|(EL|GR)[0-9]{9}|(ES)[0-9A-Z][0-9]{7}[0-9A-Z]|(FI)[0-9]{8}|(FR)[0-9A-Z]{2}[0-9]{9}|(GB)([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})|(HU)[0-9]{8}|(IE)[0-9]S[0-9]{5}L|(IT)[0-9]{11}|(LT)([0-9]{9}|[0-9]{12})|(LU)[0-9]{8}|(LV)[0-9]{11}|(MT)[0-9]{8}|(NL)[0-9]{9}B[0-9]{2}|(PL)[0-9]{10}|(PT)[0-9]{9}|(RO)[0-9]{2,10}|(SE)[0-9]{12}|(SI)[0-9]{8}|(SK)[0-9]{10})$/.test( value );
}, "<?php echo l("Špatný formát DIČ.")?>" );

$.validator.addMethod( "ico", function( value, element ) {
	return this.optional( element ) || /^([a-zA-Z]?[0-9]{2,20})$/.test( value );
}, "<?php echo l("Špatný formát IČ.")?>" );



jQuery.extend(jQuery.validator.messages, {
	required: "<?php echo l("Tento údaj je povinný.");?>",
	remote: "<?php echo l("Prosím, opravte tento údaj.");?>",
	email: "<?php echo l("Prosím, zadejte platný e-mail.");?>",
	url: "Prosím, zadejte platné URL.",
	date: "Prosím, zadejte platné datum.",
	dateISO: "Prosím, zadejte platné datum (ISO).",
	number: "<?php echo l("Prosím, zadejte číslo.");?>",
	digits: "<?php echo l("Prosím, zadávejte pouze číslice.");?>",
	creditcard: "Prosím, zadejte číslo kreditní karty.",
	equalTo: "<?php echo l("Prosím, zadejte znovu stejnou hodnotu.");?>",
	accept: "Prosím, zadejte soubor se správnou příponou.",
	maxlength: jQuery.validator.format("Prosím, zadejte nejvíce {0} znaků."),
	minlength: jQuery.validator.format("Prosím, zadejte nejméně {0} znaků."),
	rangelength: jQuery.validator.format("Prosím, zadejte od {0} do {1} znaků."),
	range: jQuery.validator.format("Prosím, zadejte hodnotu od {0} do {1}."),
	max: jQuery.validator.format("Prosím, zadejte hodnotu menší nebo rovnu {0}."),
	min: jQuery.validator.format("Prosím, zadejte hodnotu větší nebo rovnu {0}.")
});


$(document).ready(function()
{


	$( "#zastavySearch" ).autocomplete(
	{
		source: function(request, response) {
	        $.ajax({
	            url: "/ajax/zastavy/object.ajax.search",
	            type: "POST",
	            dataType: "json",
	            data: {
	                term: request.term
	            },
	            headers: {
	                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
	            },
	            success: function(data) {
	                response(data);
	            }
	        });
	    },
		minLength: 2,
		html: true,
		autoFocus: true,
		delay: 150,
		position: { my : "right top", at: "right bottom" , collision: "none" },
		change: function( event, ui )
		{
			return false;
		}
		,select: function( event, ui )
		{
			if(ui.item != null)
			{
				location.href=ui.item.redir;
			}

			return false;
		}
	});

	$( ".zakaznik" ).autocomplete(
	{
		source: function(request, response) {
	        $.ajax({
	            url: "/ajax/zakaznici/object.search-zakaznik",
	            type: "POST",
	            dataType: "json",
	            data: {
	                term: request.term
	            },
	            headers: {
	                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
	            },
	            success: function(data) {
	                response(data);
	            }
	        });
	    },
		minLength: 2,
		html: true,
		autoFocus: true,
		delay: 150,
		change: function( event, ui )
		{
			if(ui.item != null)
			{
				$("#id_zakaznik").val(ui.item.id);
				$("#addr_stat").val(ui.item.addr_stat);
				$("#pobocka").val(ui.item.pobocka);
				$("#prijmeni").val(ui.item.prijmeni);
				$('#zakazniEditkHolder').html(ui.item.data);
				$('#zakaznikInfo').html(ui.item.zakaznikinfo);

				$("textarea[name='poznamky']").val(ui.item.poznamky);

			}
			else
			{
				if($("#id_zakaznik").val()!="")
				{
					$("#id_zakaznik").val(0);
					$('#zakazniEditkHolder').html(zastavaZakaznikEditEmpty );
					$('#zakaznikInfo').html("<?php echo l('Zákazník není v databázi. Bude uložen.'); ?>");

					$("textarea[name='poznamky']").val("");
					$(".zakaznikInfoBox2").hide().html('');
					$(".zakaznikInfoBox1").fadeIn();
				}
			}

			$("#addr_stat").change();

			return false;
		}
		,select: function( event, ui )
		{
			if(ui.item != null)
			{
				$(this).val( ui.item.value );

				$("#id_zakaznik").val(ui.item.id);
				$("#addr_stat").val(ui.item.addr_stat);
				$("#pobocka").val(ui.item.pobocka);
				$("#prijmeni").val(ui.item.prijmeni);
				$('#zakazniEditkHolder').html(ui.item.data);
				$('#zakaznikInfo').html(ui.item.zakaznikinfo);

				$("textarea[name='poznamky']").val(ui.item.poznamky);
			}
			else
			{
				if($("#id_zakaznik").val()!="")
				{
					$("#id_zakaznik").val(0);
					$('#zakazniEditkHolder').html(zastavaZakaznikEditEmpty );
					$('#zakaznikInfo').html("<?php echo l('Zákazník není v databázi. Bude uložen.'); ?>");


					$("textarea[name='poznamky']").val("");
					$(".zakaznikInfoBox2").hide().html('');
					$(".zakaznikInfoBox1").fadeIn();
				}
			}

			$("#addr_stat").change();

			return false;
		}
	}).keyup(function(e)
	{
		//getAresICOdoklad($('.zakaznik'));
	});

  	$('.selectpicker').selectpicker();
	//pro potřeby admina
	$(".deleteRow").addClass("click");
	$("body").on( "click", '.deleteRow', function() {
		conf = confirm('<?php echo l("Opravdu smazat?");?>');
		if(conf)
		{
			$(this).parent().parent().remove();
		}
	});
	$("body").on( "click", '.confirm', function() {
		conf = confirm('<?php echo l("Opravdu pokračovat v akci?");?>');
		if(conf)
		{
			return true;
		}
		return false;
	});
	// end

	//$(".gallery a").fancybox({'padding': 0,'overlayShow':true,'titlePosition'  : 'over',centerOnScroll:'true',onStart: function(){ stopScroll=1; },onClosed: function(){ stopScroll=0; }});
	$(".datum").datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true,yearRange:"c-80:c+3"  });
	$("#form,#userform").validate();
	$(".validateForm").validate();

/*
	$( "#searchInput" ).autocomplete({
		source: "/ajax/core/search",
		minLength: 2,
		html: true,
		autoFocus: true,
		delay: 150,
		change: function( event, ui ) {
			if(ui.item != null)
			{
				if(ui.item.link != false)
				{
					window.location.href=ui.item.link;
				}else{
					$(this).val( ui.item.value );
				}

			}else{

			}
			return false;

		},select: function( event, ui ) {
			if(ui.item != null)
			{
				if(ui.item.link != false)
				{
					window.location.href=ui.item.link;
				}else{
					$(this).val( ui.item.value );
				}
			}else{

			}
			return false;

		}
	});*/

	// Select all
	$("button[name='select_all']").click( function() {
		$("input[type='checkbox']").prop('checked', true).change();
		return false;
	});

	// Select all
	$(".select_all").click( function() {
		$("input[type='checkbox']").prop('checked', true).change();
		return false;
	});

	// Select none
	$(".select_none").click( function() {
		$("input[type='checkbox']").prop('checked', false).change();
		return false;
	});

	// Invert selection
	$(".invert_selection").click( function() {
		$("input[type='checkbox']").each( function()
		{
			$(this).prop('checked', !$(this).prop('checked')).change();
		});
		return false;
	});

});

function checkStateTRcontrol(_this)
{
	if( $(_this).prop("checked") )
	{
		$(_this).parents("tr").addClass("active");
	}else{
		$(_this).parents("tr").removeClass("active");
	}
}


function deleteLine(typ, id)
{
	var dotaz = confirm('<?php echo l("Opravdu vymazat položku?");?>');

	if(dotaz)
	{
		$.ajax({
		type: "POST",
		url: "/ajax/core/deleteLine",
    	headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
		data: {id: id, typ: typ},
		success: function(html)
		{
			if (html=="x")
			{
				alert("<?php echo l("Vyskytla se chyba při úpravě.");?>");
			}
			else
			{
				if($('#row-'+typ+'-'+id).length != 0)
				{
					$('#row-'+typ+'-'+id).fadeOut();
				}
				else
				{
					$("#uu"+id+"").fadeOut();
				}
			}
		}
		});
	}
}
function deleteLineSoft(typ, id)
{
	var dotaz = confirm('<?php echo l("Opravdu pokračovat?");?>');

	if(dotaz)
	{
		$.ajax({
		type: "POST",
		url: "/ajax/core/deleteLine",
    	headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
		data: {id: id, typ: typ},
		success: function(html)
		{
			if (html=="x")
			{
				alert("<?php echo l("Vyskytla se chyba při úpravě.");?>");
			}
			else
			{
				if($('#row-'+typ+'-'+id).length != 0)
				{
					$('#row-'+typ+'-'+id).effect( 'pulsate', {}, 500 );
				}
				else
				{
					$("#uu"+id+"").effect( 'pulsate', {}, 500 );
				}
			}
		}
		});
	}
}
function changeLine(typ, id,styl,_this)
{
	styl = typeof styl !== 'undefined' ? styl : 'toggle';
	_this = typeof _this !== 'undefined' ? _this : $("#uu"+id+"");

	var dotaz = confirm('<?php echo l("Opravdu změnit položku?");?>');

	if(dotaz)
	{
		$.ajax({
		type: "POST",
		url: "/ajax/core/changeLine",
    	headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
		data: {id: id, typ: typ},
		success: function(html)
		{
			if (html=="x")
			{
				alert("<?php echo l("Vyskytla se chyba při úpravě.");?>");
			}
			else
			{
				if(styl=="toggle")
				{
					if(html=="1")
					{
						$(_this).removeClass("text-danger fa-times-circle").addClass('text-success fa-check-circle');
					}
					else
					{
						$(_this).removeClass("text-success fa-check-circle").addClass('text-danger fa-times-circle');
					}
				}
				else
				{

				}
				$("#uu"+id+"").find(".change").fadeOut();
			}
		}
		});
	}
}
function JSemailSend(email,predmet,text)
{
	$.ajax({
		type: "POST",
		url: "/ajax/core/emailSend",
    	headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
		data: {email:email,predmet: predmet, text: text},
		success: function(html){
			if (html=="x"){ alert("<?php echo l("Vyskytla se chyba při odesílání.");?>"); }
			else{

			}
		}
		});
}
function formSubmit()
{
	$("#form").submit();
}

function bytesToSize(bytes, precision) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    var posttxt = 0;
    if (bytes == 0) return 'n/a';
    while( bytes >= 1024 ) {
        posttxt++;
        bytes = bytes / 1024;
    }
    return parseInt(bytes).toFixed(precision) + " " + sizes[posttxt];
}

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

function chat_addToText(str,target)
{
	if (target === undefined) { target = '#messageSend'; }
	$(target).val(str);
}

function strtotime(text, now) {
  //  discuss at: http://phpjs.org/functions/strtotime/
  //     version: 1109.2016
  // original by: Caio Ariede (http://caioariede.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Caio Ariede (http://caioariede.com)
  // improved by: A. Matías Quezada (http://amatiasq.com)
  // improved by: preuter
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Mirko Faber
  //    input by: David
  // bugfixed by: Wagner B. Soares
  // bugfixed by: Artur Tchernychev
  // bugfixed by: Stephan Bösch-Plepelits (http://github.com/plepe)
  //        note: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
  //   example 1: strtotime('+1 day', 1129633200);
  //   returns 1: 1129719600
  //   example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
  //   returns 2: 1130425202
  //   example 3: strtotime('last month', 1129633200);
  //   returns 3: 1127041200
  //   example 4: strtotime('2009-05-04 08:30:00 GMT');
  //   returns 4: 1241425800
  //   example 5: strtotime('2009-05-04 08:30:00+00');
  //   returns 5: 1241425800
  //   example 6: strtotime('2009-05-04 08:30:00+02:00');
  //   returns 6: 1241418600
  //   example 7: strtotime('2009-05-04T08:30:00Z');
  //   returns 7: 1241425800

  var parsed, match, today, year, date, days, ranges, len, times, regex, i, fail = false;

  if (!text) {
    return fail;
  }

  // Unecessary spaces
  text = text.replace(/^\s+|\s+$/g, '')
    .replace(/\s{2,}/g, ' ')
    .replace(/[\t\r\n]/g, '')
    .toLowerCase();

  // in contrast to php, js Date.parse function interprets:
  // dates given as yyyy-mm-dd as in timezone: UTC,
  // dates with "." or "-" as MDY instead of DMY
  // dates with two-digit years differently
  // etc...etc...
  // ...therefore we manually parse lots of common date formats
  match = text.match(
    /^(\d{1,4})([\-\.\/\:])(\d{1,2})([\-\.\/\:])(\d{1,4})(?:\s(\d{1,2}):(\d{2})?:?(\d{2})?)?(?:\s([A-Z]+)?)?$/);

  if (match && match[2] === match[4]) {
    if (match[1] > 1901) {
      switch (match[2]) {
      case '-': {
        // YYYY-M-D
        if (match[3] > 12 || match[5] > 31) {
          return fail;
        }

        return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
      }
      case '.': {
        // YYYY.M.D is not parsed by strtotime()
        return fail;
      }
      case '/': {
        // YYYY/M/D
        if (match[3] > 12 || match[5] > 31) {
          return fail;
        }

        return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
      }
      }
    } else if (match[5] > 1901) {
      switch (match[2]) {
      case '-': {
        // D-M-YYYY
        if (match[3] > 12 || match[1] > 31) {
          return fail;
        }

        return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
      }
      case '.': {
        // D.M.YYYY
        if (match[3] > 12 || match[1] > 31) {
          return fail;
        }

        return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
      }
      case '/': {
        // M/D/YYYY
        if (match[1] > 12 || match[3] > 31) {
          return fail;
        }

        return new Date(match[5], parseInt(match[1], 10) - 1, match[3],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
      }
      }
    } else {
      switch (match[2]) {
      case '-': {
        // YY-M-D
        if (match[3] > 12 || match[5] > 31 || (match[1] < 70 && match[1] > 38)) {
          return fail;
        }

        year = match[1] >= 0 && match[1] <= 38 ? +match[1] + 2000 : match[1];
        return new Date(year, parseInt(match[3], 10) - 1, match[5],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
      }
      case '.': {
        // D.M.YY or H.MM.SS
        if (match[5] >= 70) {
          // D.M.YY
          if (match[3] > 12 || match[1] > 31) {
            return fail;
          }

          return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
        }
        if (match[5] < 60 && !match[6]) {
          // H.MM.SS
          if (match[1] > 23 || match[3] > 59) {
            return fail;
          }

          today = new Date();
          return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
            match[1] || 0, match[3] || 0, match[5] || 0, match[9] || 0) / 1000;
        }

        // invalid format, cannot be parsed
        return fail;
      }
      case '/': {
        // M/D/YY
        if (match[1] > 12 || match[3] > 31 || (match[5] < 70 && match[5] > 38)) {
          return fail;
        }

        year = match[5] >= 0 && match[5] <= 38 ? +match[5] + 2000 : match[5];
        return new Date(year, parseInt(match[1], 10) - 1, match[3],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
      }
      case ':': {
        // HH:MM:SS
        if (match[1] > 23 || match[3] > 59 || match[5] > 59) {
          return fail;
        }

        today = new Date();
        return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
          match[1] || 0, match[3] || 0, match[5] || 0) / 1000;
      }
      }
    }
  }

  // other formats and "now" should be parsed by Date.parse()
  if (text === 'now') {
    return now === null || isNaN(now) ? new Date()
      .getTime() / 1000 | 0 : now | 0;
  }
  if (!isNaN(parsed = Date.parse(text))) {
    return parsed / 1000 | 0;
  }
  // Browsers != Chrome have problems parsing ISO 8601 date strings, as they do
  // not accept lower case characters, space, or shortened time zones.
  // Therefore, fix these problems and try again.
  // Examples:
  //   2015-04-15 20:33:59+02
  //   2015-04-15 20:33:59z
  //   2015-04-15t20:33:59+02:00
  if (match = text.match(
      /^([0-9]{4}-[0-9]{2}-[0-9]{2})[ t]([0-9]{2}:[0-9]{2}:[0-9]{2}(\.[0-9]+)?)([\+-][0-9]{2}(:[0-9]{2})?|z)/)) {
    // fix time zone information
    if (match[4] == 'z') {
      match[4] = 'Z';
    } else if (match[4].match(/^([\+-][0-9]{2})$/)) {
      match[4] = match[4] + ':00';
    }

    if (!isNaN(parsed = Date.parse(match[1] + 'T' + match[2] + match[4]))) {
      return parsed / 1000 | 0;
    }
  }

  date = now ? new Date(now * 1000) : new Date();
  days = {
    'sun' : 0,
    'mon' : 1,
    'tue' : 2,
    'wed' : 3,
    'thu' : 4,
    'fri' : 5,
    'sat' : 6
  };
  ranges = {
    'yea' : 'FullYear',
    'mon' : 'Month',
    'day' : 'Date',
    'hou' : 'Hours',
    'min' : 'Minutes',
    'sec' : 'Seconds'
  };

  function lastNext(type, range, modifier) {
    var diff, day = days[range];

    if (typeof day !== 'undefined') {
      diff = day - date.getDay();

      if (diff === 0) {
        diff = 7 * modifier;
      } else if (diff > 0 && type === 'last') {
        diff -= 7;
      } else if (diff < 0 && type === 'next') {
        diff += 7;
      }

      date.setDate(date.getDate() + diff);
    }
  }

  function process(val) {
    var splt = val.split(' '), // Todo: Reconcile this with regex using \s, taking into account browser issues with split and regexes
      type = splt[0],
      range = splt[1].substring(0, 3),
      typeIsNumber = /\d+/.test(type),
      ago = splt[2] === 'ago',
      num = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);

    if (typeIsNumber) {
      num *= parseInt(type, 10);
    }

    if (ranges.hasOwnProperty(range) && !splt[1].match(/^mon(day|\.)?$/i)) {
      return date['set' + ranges[range]](date['get' + ranges[range]]() + num);
    }

    if (range === 'wee') {
      return date.setDate(date.getDate() + (num * 7));
    }

    if (type === 'next' || type === 'last') {
      lastNext(type, range, num);
    } else if (!typeIsNumber) {
      return false;
    }

    return true;
  }

  times = '(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec' +
    '|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?' +
    '|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)';
  regex = '([+-]?\\d+\\s' + times + '|' + '(last|next)\\s' + times + ')(\\sago)?';

  match = text.match(new RegExp(regex, 'gi'));
  if (!match) {
    return fail;
  }

  for (i = 0, len = match.length; i < len; i++) {
    if (!process(match[i])) {
      return fail;
    }
  }

  // ECMAScript 5 only
  // if (!match.every(process))
  //    return false;

  return (date.getTime() / 1000);
}


function getC4CAresICO(_thisClick)
{
	var zdokladu = 0;
	var _this = $("#ico");
	var _thisStat = $("#addr_stat").val();

	if(_thisStat=='')
	{
		alert('<?php echo l('Nejdříve vyberte stát.') ?>');
		return;
	}

	if(_thisStat!='cz' && _thisStat!='sk')
	{
		alert('<?php echo l('Získání dat dle IČ je možné pouze u Českých a Slovenských společností. U ostatních možné použe dle DIČ z Evropské databáze.') ?>');
		return;
	}

	if(_this.val() == '')
	{
		alert('<?php echo l('IČ není vyplněné.') ?>');
		return;
	}

	var puvodniInner = $(_thisClick).html();

	$(_thisClick).html('<i class="fa fa-cog fa-spin"></i> Pracuji, vyčkejte...');
	_this.prop('disabled',true);
		$.ajax(
		{
			type: "POST",
			data: {value: _this.val(),stat:_thisStat},
			url: "ajax/zakaznici/object.getICO",
    		headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
			success: function(html)
			{
				$(_thisClick).html(puvodniInner);
				_this.prop('disabled',false);
				if(html == "false")
				{
					alert('Bohužel se nepodařilo získat potřebná data. Vyplňte informace ručně, nebo to zkuste znovu.');
				}else{
					var obj = jQuery.parseJSON(html);

					if (typeof(obj.spolecnost) != "undefined")
					{
						$("input[name='FAKnazev']").val(obj.spolecnost);
						$("input[name='FAKspolecnost']").val(obj.spolecnost);
						$("input[name='spolecnost']").val(obj.spolecnost);
						$("input[name='prijmeni']").val(obj.spolecnost);
					}
					if (typeof(obj.psc) != "undefined")
					{
						$("input[name='FAKpsc']").val(obj.psc);
						$("input[name='DORpsc']").val(obj.psc);
						$("input[name='psc']").val(obj.psc);
						$("input[name='addr_psc']").val(obj.psc);

					}
					if (typeof(obj.mesto) != "undefined")
					{
						$("input[name='FAKmesto']").val(obj.mesto);
						$("input[name='DORmesto']").val(obj.mesto);
						$("input[name='mesto']").val(obj.mesto);
						$("input[name='addr_mesto']").val(obj.mesto);
					}
					if (typeof(obj.ulice) != "undefined")
					{
						$("input[name='FAKulice']").val(obj.ulice + " " + obj.cp);
						$("input[name='DORulice']").val(obj.ulice + " " + obj.cp);
						$("input[name='ulice']").val(obj.ulice + " " + obj.cp);
						$("input[name='bydliste']").val(obj.ulice + " " + obj.cp);
					}
					if (typeof(obj.cp) != "undefined")
					{
						$("input[name='FAKulice']").val($("input[name='FAKulice']").val() + " " + obj.cp);
						$("input[name='DORulice']").val($("input[name='DORulice']").val() + " " + obj.cp);
						$("input[name='ulice']").val($("input[name='ulice']").val() + " " + obj.cp);
						$("input[name='bydliste']").val($("input[name='bydliste']").val() + " " + obj.cp);
					}

					if (typeof(obj.dic) != "undefined")
					{
						$("input[name='dic']").val($("input[name='dic']").val());
					}

					if (typeof(obj.platce) != "undefined" && obj.platce == 1)
					{
						$('input:radio[name="platce"]').filter('[value="'+obj.platce+'"]').prop('checked', true);
						$('input:checkbox[name="platce_dph"]').prop('checked', true);

						alert('<?php echo l('Společnost je plátce DPH.');?>')
					}
					else
					{
						$('input:radio[name="platce"]').filter('[value="0"]').prop('checked', true);
						$('input:checkbox[name="platce_dph"]').prop('checked', false);
					}


					if (typeof(obj.email) != "undefined")
					{
						$("#email").val(obj.email);
					}
					if (typeof(obj.tel) != "undefined")
					{
						$("#telefon").val(obj.tel);
					}
					if (typeof(obj.dic) != "undefined")
					{
						$("#dic").val(obj.dic);
					}
					if (typeof(obj.web) != "undefined")
					{
						$("#www").val(obj.web);
					}
				}

			}
		});
}

function getC4CVINdecoder(_thisClick)
{
	var zdokladu = 0;
	var _this = $("#auto_vin");


	if(_this.val() == '')
	{
		alert('<?php echo l('VIN není vyplněné.') ?>');
		return;
	}

	var puvodniInner = $(_thisClick).html();

	$(_thisClick).html('<i class="fa fa-cog fa-spin"></i> Pracuji, vyčkejte...');
	_this.prop('disabled',true);
		$.ajax(
		{
			type: "GET",
			data: {vin: _this.val()},
			url: "ajax/vindecoder/object.ajax.vindecoder",
    		headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
			success: function(html)
			{
				$(_thisClick).html(puvodniInner);
				_this.prop('disabled',false);
				if(html == "false")
				{
					alert('Bohužel se nepodařilo získat potřebná data. Vyplňte informace ručně, nebo to zkuste znovu.');
				}
				else if(html.substr(0, 6) == "Chyba:")
				{
					alert(html);
				}
				else
				{
					var obj = jQuery.parseJSON(html);
					console.log(obj);

					var cars = ['auto_znacka','auto_model','auto_barva','auto_rokvyroby','auto_registrace','auto_vykon','auto_objem','auto_hmotnost','auto_prevodovka','auto_palivo','auto_karoserie'];

					for (const element of cars)
					{
						console.log(element);
					    if (typeof(obj[element]) != "undefined")
						{
							$("#"+element).val(obj[element]).change();
						}
					}
				}
			}
		});
}


function getC4CAresDIC(_thisClick)
{
	var zdokladu = 0;
	var _this = $("#dic");
	var _thisStat = $("#addr_stat").val();

	if(_this.val() == '')
	{
		alert('<?php echo l('DIČ není vyplněné.') ?>');
		return;
	}

	var puvodniInner = $(_thisClick).html();

	$(_thisClick).html('<i class="fa fa-cog fa-spin"></i> Pracuji, vyčkejte...');
	_this.prop('disabled',true);
		$.ajax(
		{
			type: "POST",
			data: {value: _this.val(),stat:_thisStat},
			url: "ajax/zakaznici/object.getDIC",
    		headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
			success: function(html)
			{
				$(_thisClick).html(puvodniInner);
				_this.prop('disabled',false);
				if(html == "down")
				{
					alert('<?php echo l('Nepodařilo se připojit k VIES databázi. Další pokus o připojení je možný až za 5 minut. Ověřte firmu ručně.'); ?>');
				}else if(html == "false")
				{
					alert('<?php echo l('Společnost nebyla nalezena v databázi VIES.'); ?>');

					$('input:checkbox[name="platce_dph"]').prop('checked', false);

				}else{
					var obj = jQuery.parseJSON(html);

					if (typeof(obj.spolecnost) != "undefined")
					{
						$("input[name='FAKnazev']").val(obj.spolecnost);
						$("input[name='FAKspolecnost']").val(obj.spolecnost);
						$("input[name='spolecnost']").val(obj.spolecnost);
						$("input[name='prijmeni']").val(obj.spolecnost);
					}
					if (typeof(obj.psc) != "undefined")
					{
						$("input[name='FAKpsc']").val(obj.psc);
						$("input[name='DORpsc']").val(obj.psc);
						$("input[name='psc']").val(obj.psc);
						$("input[name='addr_psc']").val(obj.psc);

					}
					if (typeof(obj.mesto) != "undefined")
					{
						$("input[name='FAKmesto']").val(obj.mesto);
						$("input[name='DORmesto']").val(obj.mesto);
						$("input[name='mesto']").val(obj.mesto);
						$("input[name='addr_mesto']").val(obj.mesto);
					}
					if (typeof(obj.ulice) != "undefined")
					{
						$("input[name='FAKulice']").val(obj.ulice + " " + obj.cp);
						$("input[name='DORulice']").val(obj.ulice + " " + obj.cp);
						$("input[name='ulice']").val(obj.ulice + " " + obj.cp);
						$("input[name='bydliste']").val(obj.ulice + " " + obj.cp);
					}
					if (typeof(obj.cp) != "undefined")
					{
						$("input[name='FAKulice']").val($("input[name='FAKulice']").val() + " " + obj.cp);
						$("input[name='DORulice']").val($("input[name='DORulice']").val() + " " + obj.cp);
						$("input[name='ulice']").val($("input[name='ulice']").val() + " " + obj.cp);
						$("input[name='bydliste']").val($("input[name='bydliste']").val() + " " + obj.cp);
					}

					if (typeof(obj.ico) != "undefined" && obj.ico != '')
					{
						$("input[name='ico']").val($("input[name='ico']").val());
					}

					$('input:radio[name="platce"]').filter('[value="1"]').prop('checked', true);
					$('input:checkbox[name="platce_dph"]').prop('checked', true);
				}

			}
		});
}



/*
2017-12-13
*/
function coreGeneratePassword(saveTo)
{

    if (parseInt(navigator.appVersion) <= 3) {
        alert("Sorry this only works in 4.0+ browsers");
        return true;
    }
    var length=11;
    var sPassword = "";

    var noPunction = true;
    var randomLength = false;

    if (randomLength) {
        length = Math.random();

        length = parseInt(length * 100);
        length = (length % 7) + 6
    }

    for (i=0; i < length; i++)
    {
        numI = getRandomNum();
        if (noPunction) { while (checkPunc(numI)) { numI = getRandomNum(); } }

        sPassword = sPassword + String.fromCharCode(numI);
    }
    $(saveTo).val(sPassword);
}

function getRandomNum()
{

    // between 0 - 1
    var rndNum = Math.random()
    // rndNum from 0 - 1000
    rndNum = parseInt(rndNum * 1000);
    // rndNum from 33 - 127
    rndNum = (rndNum % 94) + 33;
    return rndNum;
}

function checkPunc(num)
{

    if ((num >=33) && (num <=47)) { return true; }
    if ((num >=58) && (num <=64)) { return true; }
    if ((num >=91) && (num <=96)) { return true; }
    if ((num >=123) && (num <=126)) { return true; }

    return false;
}

function manageOverlay(show, text) {
    let overlay = $('#processing-overlay');

    // Create the overlay if it doesn't exist and we need to show it
    if (show && overlay.length === 0) {
    // Create elements with jQuery
    const statusBox = $('<div id="processing-status"></div>');
    overlay = $('<div id="processing-overlay"></div>').append(statusBox);

        // Apply styles directly
    overlay.css({
        'position': 'fixed',
        'top': 0,
        'left': 0,
        'width': '100%',
        'height': '100%',
        'backgroundColor': 'rgba(0, 0, 0, 0.7)',
        'zIndex': 9999,
        'display': 'flex',
        'justifyContent': 'center',
        'alignItems': 'center'
    });

    statusBox.css({
        'padding': '20px 40px',
        'backgroundColor': '#fff',
        'borderRadius': '8px',
        'fontSize': '1.2em',
        'color': '#333'
    });

        // Add the overlay to the page
        $('body').append(overlay);
    }

    if (show) {
        // Update text and ensure it's visible
        overlay.find('#processing-status').text(text);
        overlay.show();
    } else {
        // Hide and remove the overlay from the DOM
        overlay.fadeOut(400, function() {
            $(this).remove();
        });
    }
}

async function exportToDaktela(type) {
    let dotaz = confirm('<?php echo l("Opravdu exportovat do Daktely?");?>');
    if ( dotaz ) {
        const ids = $('#data-poptavky-ids').data('poptavky-ids');
        let idArray = String(ids ?? '').split(',').filter(Boolean).map(Number);
        const totalCount = idArray.length;

        try {
        manageOverlay(true, 'Starting...');

        for (const [index, id] of idArray.entries()) {
            const currentIndex = index + 1;
            const percentage = Math.round((currentIndex / totalCount) * 100);
            const statusText = `Processing ${currentIndex} / ${totalCount} (${percentage}%)`;

            // Update the overlay's status text
            manageOverlay(true, statusText);
            console.log(statusText);

            const response = await $.ajax({
                url: '/ajax/daktela/object.ajax.daktelaexport',
                type: 'POST',
                data: {
                    type: type,
                    poptavka_id: id
                }
            });

            //if ( response === 'exists' ) {
            //   console.log('Record already exists in Daktela for ID: ' + id);
            //} else if ( response === 'not_exists' ) {
            //    console.log('No record found in Daktela for ID: ' + id);
            //} else {
            //    console.log('Error for ID: ' + id );
            //}

        }

        manageOverlay(true, 'All requests completed! ✅');
        setTimeout(() => manageOverlay(false), 1500);

        } catch (error) {
            const errorMessage = 'An error occurred. Please try again.';
            manageOverlay(true, errorMessage);
            console.error(`Request failed.`, error.responseText || error);
            setTimeout(() => manageOverlay(false), 3000);
        }
    }
}

// ecomail filter export modal
$(function() {
    const DIALOG_ID = '#ecomailDialog';
    const LIST_SELECT_ID = '#ecomailList';
    const LOADING_ID = '#loadingIndicator';

    // *** Zástupná funkce pro volání Ecomail API (MOCK) ***
    // REÁLNÉ ŘEŠENÍ: ZDE MUSÍ BÝT VOLÁNÍ VAŠEHO SERVERU (backend proxy),
    // který bezpečně zavolá: https://api2.ecomailapp.cz/lists
    function fetchEcomailLists() {
        $(LOADING_ID).show();
        $(LIST_SELECT_ID).prop('disabled', true).html('<option value="">Načítám...</option>');

        // Simulace asynchronního volání API
        return fetch("/object/ecomail/object.ecomail-list")
			.then(response => {
				if (!response.ok) {
				throw new Error('Network response was not ok ' + response.statusText);
				}
				return response.json(); // <-- parse JSON here
			})
			.then(data => {
				console.log('Data received:', data.lists.body);

				// now you have the real data object
				populateListSelect(data.lists.body);

				$(LOADING_ID).hide();
			})
			.catch(error => {
				console.error('Fetch error:', error);
				$(LOADING_ID).hide();
			});
	}

    // Funkce pro naplnění selectu
    function populateListSelect(lists) {
        let options = '<option value="">-- Vyberte seznam --</option>';
        if (lists && lists.length > 0) {
            lists.forEach(list => {
                options += `<option value="${list.id}">${list.name}</option>`;
            });
            $(LIST_SELECT_ID).prop('disabled', false);
        } else {
            options = '<option value="">Žádné seznamy nenalezeny.</option>';
            $(LIST_SELECT_ID).prop('disabled', true);
        }
        $(LIST_SELECT_ID).html(options);
    }

    // Inicializace JQuery UI Dialogu
    $(DIALOG_ID).dialog({
        autoOpen: false,
        modal: true,
        width: 400,
        height: 'auto',
        buttons: {
			"Exportovat": function() {
				const country = $('#countrySelect').val();
				const listId = $(LIST_SELECT_ID).val();
				const months = $('#monthsBack').val();

				if (!listId) {
					alert("Prosím, vyberte Ecomail seznam.");
					return;
				}

				const settings = {
					countryId: country,
					listId: listId,
					dateFrom: months
				};

				fetch("/object/ecomail/object.poptavka.export", {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify(settings)
				})
				.then(response => {
					if (!response.ok) {
						throw new Error("Chyba serveru: " + response.statusText);
					}
					return response.json();
				})
				.then(data => {
					console.log("Odpověď backendu:", data);
					alert("Nastavení uloženo");
				})
				.catch(error => {
					console.error("Chyba při exportu:", error);
					alert("Export se nezdařil!");
				});

				$(this).dialog("close");
			},
			"Zavřít": function() {
				$(this).dialog("close");
			}
		},
        open: async function() {
            // Načíst data z Ecomailu při otevření dialogu
            const lists = await fetchEcomailLists();
            populateListSelect(lists);
        }
    });

    // Tlačítko pro otevření dialogu
    $(document).on('click', '#openDialogEcomail', function() {
        $(DIALOG_ID).dialog('open');
		fetchEcomailLists();
    });

});




<? echo "/*";?></script><? echo "*/";?>