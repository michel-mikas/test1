<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.1.4 -->
<script src="https://code.jquery.com/jquery-2.2.2.min.js" integrity="sha256-36cp2Co+/62rEAAYHLmRCPIych47CvdM+uTBJwSzWjI=" crossorigin="anonymous"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

<!-- Datatables -->
@if(isset($datatable))
	<script type="text/javascript" src="https://cdn.datatables.net/t/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.11,b-1.1.2,b-colvis-1.1.2,b-html5-1.1.2,b-print-1.1.2,r-2.0.2/datatables.min.js"></script>
@endif

<!-- datepicker -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

<!-- AdminLTE App -->
<script type="text/javascript" src="{{url('/js/admin_panel.js')}}"></script>

<script type="text/javascript">
	var baseUrl = "{{url('/')}}";
	var url = "{{Request::url()}}";
	@if(isset($datatable))
		@if($datatable['server_side'])
			datatables_server();
		@else
			datatables_client();
		@endif
	@endif
</script>