// DATATABLES
$(document).ready(function(){
	
	function datatables_server () {
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
		                    data.inputMaxDate = (Date.now()+1000)/1000;
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

});