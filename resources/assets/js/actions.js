$(window).on('load', function() {
    $('.loader-page').fadeOut(250);
});
$(document).ready(function() {
	    // ACTIONS

    $('body').on('click', '.hasModal', function(event) {

        event.preventDefault();

        $('#confirm .delModal').html('');

        var btnText = $(this).data('btnconfirm');
        var question = $(this).data('question');
        var name = $(this).data('name');
        var urlToGo = $(this).prop('href');
        var advice = $(this).data('advice');
        var textClass = $(this).data('textclass');

        if(advice != '') {
            $('#confirm .delModal').append(question+ ' <b>' +name+ '</b><p class="' +textClass+ '"><b>' +advice+ '</b></p>');
        }
        else {
            $('#confirm .delModal').append(question+ ' <b>' +name+ '</b>');
        }

        $('#confirm .confirmBtn').prop('href', urlToGo);
        $('#confirm .confirmBtn').text(btnText);
        $('#confirm').modal('show');

        return false;

    });

    $('button.hide_text').on('click', function(){
		if($(this).find('i').hasClass('fa-plus')) {
			var links = $(this).parent().find('.hidden');
			links.removeClass('hidden');
			links.addClass('show');
			$(this).find('i').removeClass('fa-plus');
			$(this).find('i').addClass('fa-minus');
		}
		else {
			var links = $(this).parent().find('.show');
			links.addClass('hidden');
			links.removeClass('show');
			$(this).find('i').removeClass('fa-minus');
			$(this).find('i').addClass('fa-plus');
		}
	});

});

function datatables_server() {
	$('.minDate, .maxDate').datepicker({
        dateFormat: 'dd-mm-yy',
        showAnim: "slideDown",
    });


    function to_unix_timestamp(myDate) {
        if(typeof myDate !== 'undefined' && myDate !== ''){
            myDate=myDate.split("-");
            var newDate=myDate[1]+"/"+myDate[0]+"/"+myDate[2];
            return new Date(newDate).getTime();
        }
    }

    var count = 1;
    var columnsNames = [];
    $('thead th').each(function() {
        columnsNames.push({'data': $(this).data('name')});
    });

    var table = $('.myDataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: url,
            data: function (data) {
                var columnName = $('.interval_inputs').data('comlumnname');
                if($('.interval_inputs').length > 0) {
                    var minDate = $('.minDate').val();
                    var maxDate = $('.maxDate').val();
                    if(minDate == '' && maxDate == '') {
                        data.inputMinDate = 0;
                        data.inputMaxDate = null;
                    }
                    else {
                        data.inputMinDate = to_unix_timestamp(minDate)/1000;
                        data.inputMaxDate = to_unix_timestamp(maxDate)/1000;
                    }
                    data.columnName = columnName;
                }
            }
        },
        "columns": columnsNames,
        "createdRow": function ( row, data, index ) {
            $('td', row).last().addClass('actions');
        },
        responsive: true,
    });
    
    $('#wrapper_'+count+' .box-body').prepend('<div class="btnsContainer"></div>');
    $('#wrapper_'+count+' .box-body .btnsContainer').append(table.buttons().container());

    $('.minDate, .maxDate').on('change', function() {
        var minDate = $('.minDate').val();
        var maxDate = $('.maxDate').val();

        if(minDate != '' && maxDate != '') {
            if(to_unix_timestamp(minDate) <= to_unix_timestamp(maxDate)) {
                table.draw();
            }
        }
        else if(minDate == '' && maxDate == '') {
            table.draw();
        }
    });
}

function datatables_client() {
	$('.minDate, .maxDate').datepicker({
		dateFormat: 'dd-mm-yy',
		showAnim: "slideDown",
	});

	function to_unix_timestamp(myDate) {
		if(myDate != undefined){
			myDate=myDate.split("-");
			var newDate=myDate[1]+"/"+myDate[0]+"/"+myDate[2];
			return new Date(newDate).getTime();
		}
	}

	function date_is_valid(time_now) {
		var date = new Date(time_now);
		if(date.getTime() > 0) { // if valid timestamp
			return true;
		}
		return false;
	}

	if(window.location.hash) {
		var time_now = (window.location.hash.substr(1) * 1000);
	    if(date_is_valid(time_now)) { // if valid timestamp
	    	var date = new Date(time_now);
	    	var month = date.getMonth()+1;
	    	var today = date.getDate()+ '-' +month+ '-' +date.getFullYear();
	    	var weekAgo = date.getDate() - 7;
	    	var weekAgo = weekAgo+ '-' +month+ '-' +date.getFullYear();
			$('.minDate').val(today);
			$('.maxDate').val(weekAgo);
	    }
	    else {
	    	$('.minDate').val('');
			$('.maxDate').val('');
	    }
	}
	else {
		$('.minDate').val('');
		$('.maxDate').val('');
	}

	var columnIndex = $('.interval_inputs').data('columnfilter');

	$.fn.dataTable.ext.search.push(
		function( settings, data, dataIndex ) {

			var inputMinDate = $('.minDate').val();
	        var inputMaxDate = $('.maxDate').val();

	        var unixTimeMin = to_unix_timestamp(inputMinDate);
	        var unixTimeMax = to_unix_timestamp(inputMaxDate);
			var unixTimeRow = to_unix_timestamp(data[columnIndex]);
			
			if(unixTimeMin <= unixTimeMax && date_is_valid(unixTimeRow)) {
				/*console.log('row date: ' +unixTimeRow+ ' - ' +data[columnIndex]);
				console.log('min date: ' +unixTimeMin+ ' - ' +$(inputMinDate).val())
				console.log('max date: ' +unixTimeMax+ ' - ' +$(inputMaxDate).val());*/
				if(unixTimeRow >= unixTimeMin && unixTimeRow <= unixTimeMax) {
					return true;
				}
				return false;
			}
			return true;
		}
	);

	var count = 1;
	var tables = {};
	$('.myDataTable').each(function() {
	
		tables[count] = $(this).DataTable({
			initComplete: function () {
		        this.api().columns().every( function () {
		            var column = this;
		            var select = $('<select><option value=""></option></select>')
		                .appendTo( $(column.footer()).empty() )
		                .on( 'change', function () {
		                    var val = $.fn.dataTable.util.escapeRegex(
		                        $(this).val()
		                    );
	 
		                    column
		                        .search( val ? '^'+val+'$' : '', true, false )
		                        .draw();
		                } );
	 
		            column.data().unique().sort().each( function ( d, j ) {
		                select.append( '<option value="'+d+'">'+d+'</option>' )
		            } );
		        } );
		        $('tfoot th:last-child select').remove();
		    },
		    buttons: [
		        {
		            extend: 'print',
		            exportOptions: {
		                columns: ':visible'
		            }
		        },
		        {
		            extend: 'excel',
		            exportOptions: {
		                columns: ':visible'
		            }
		        },
		        {
		            extend: 'pdf',
		            exportOptions: {
		                columns: ':visible'
		            }
		        },
		        'colvis'
		    ],
		    responsive: true,
		});

		$('#wrapper_'+count+' .box-body').prepend('<div class="btnsContainer"></div>');
		$('#wrapper_'+count+' .box-body .btnsContainer').append(tables[count].buttons().container());
		count += 1;
	});


	$('.minDate, .maxDate').on('change', function() {
		var minDate = $('.minDate').val();
		var maxDate = $('.maxDate').val();

		if(minDate != '' && maxDate != '') {
			for (count in tables) {
				tables[count].draw();
			}
		}
		else if(minDate == '' && maxDate == '') {
			for (count in tables) {
				tables[count].draw();
			}
		}
	});
}