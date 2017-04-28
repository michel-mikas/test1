@extends('admin_panel/dashboard')

@section('content')

<div class="row">
	<div class="col-xs-12">
		@if(Session::has('flash_success'))
		    <div class="alert alert-success text-left col-sm-3">
		        <b>{{Session::get('flash_success')}}</b>
		    </div>
	    @endif
		
		@if(Session::has('flash_error'))
	        <div class="alert alert-danger text-left col-sm-3">
	            <b>{{Session::get('flash_error')}}</b>
	        </div>
    	@endif
		
		@if(count($errors->all()) > 0)
	        <div class="alert alert-danger text-left col-sm-3">
	    	@foreach ($errors->all() as $error)
		        <b>&#8226; {{$error}}</b><br>
	    	@endforeach
	    	</div>
		@endif
	</div>
	<div class="col-xs-12">
		<div class="inspectParams">
			@foreach($data['inspect'] as $param => $value)
				<p><b>{!!$param!!}: </b>{!!$value!!}</p>
			@endforeach
			<br>
	        @if(isset($data['buttons']))
		        @foreach($data['buttons'] as $action)
	                @if(isset($action['modal']))
	                    <a data-question="{{$action['modal']['question']}}" data-name="{{$action['modal']['name']}}" href="{{url($action['href'])}}" title="{{$action['title']}}" data-advice="{{$action['modal']['advice_phrase']}}" data-textclass="{{$action['modal']['class_phrase']}}" data-btnconfirm="{{$action['modal']['btn_confirm']}}" class="{{$action['linkClass']}} hasModal"><span class="{{$action['spanClass']}}"></span></a>
	                @else
	                    <a title="{{$action['title']}}" href="{{url($action['href'])}}" class="{{$action['linkClass']}}"><span class="{{$action['spanClass']}}"></span></a>
	                @endif
	            @endforeach
	        @endif
		</div>
	</div>
</div>
@endsection