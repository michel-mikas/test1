@extends('admin_panel/dashboard')

@section('content')
<div class="row">
	<div class="col-sm-5">
		<form method="POST" enctype="multipart/form-data" action="{{url($data['form_params']['action'])}}">
			
			@if(Session::has('flash_success'))
			    <div class="alert alert-success">
			        <b>{{Session::get('flash_success')}}</b>
			    </div>
		    @endif
			
			@if(Session::has('flash_error'))
		        <div class="alert alert-danger text-left">
		            <b>{{Session::get('flash_error')}}</b>
		        </div>
	    	@endif
			
			@if(count($errors->all()) > 0)
		        <div class="alert alert-danger text-left">
		    	@foreach ($errors->all() as $error)
			        <b>&#8226; {{$error}}</b><br>
		    	@endforeach
		    	</div>
			@endif

			@foreach($data['form_params']['inputs'] as $key => $input)
				@if($input['type'] == 'select')
					<?php $param = $input['param']; ?>
					<fieldset class="form-group">
						<label for="{{ $input['select_props']['id'] }}">{!! $input['label'] !!}</label>
						<div class="input-group {{$input['parent_class']}}">
							<span class="input-group-addon">{!! $input['input-group-addon'] !!}</span>
							<select @foreach($input['select_props'] as $prop => $value) {{$prop}}="{{$value}}"@endforeach>
								@if(isset($input['opt1']))
									{!! $input['opt1'] !!}
								@endif
								@foreach($input['opts'] as $opt)
									<option @foreach($opt->props as $prop => $value) {{$prop}}="{{$value}}"@endforeach>{{$opt->opt_text}}</option>
								@endforeach
							</select>
						</div>
					</fieldset>
				@elseif($input['type'] == 'checkbox' || $input['type'] == 'radio')
					<?php $param = $input['param']; ?>
					<fieldset class="form-group">
						<label for="{{ $input['id'] }}">{!! $input['input-group-addon'] !!} {!! $input['label'] !!}</label>
						@foreach($input['opts'] as $opt)
							<div class="{{$input['parent_class']}}">
								<input @foreach($opt->props as $prop => $value) {{$prop}}="{{$value}}"@endforeach> <span>{!!$opt->opt_text!!}</span>
							</div>
						@endforeach
					</fieldset>
				@elseif($input['type'] == 'textarea')
					<fieldset class="form-group">
						<label for="{{$input['props']['id']}}">{{$input['label']}}</label>
  						<textarea @foreach($input['props'] as $prop => $value) {{$prop}}="{{$value}}"@endforeach>{{$input['value']}}</textarea>
					</fieldset>
				@elseif($input['type'] == 'file')
					<fieldset class="form-group">
						<label for="{{$input['props']['id']}}">{{$input['label']}}</label>
						<input @foreach($input['props'] as $prop => $value) {{$prop}}="{{$value}}"@endforeach>
					</fieldset>
				@elseif($input['type'] == 'hidden')
					<input @foreach($input['props'] as $prop => $value) {{$prop}}="{{$value}}"@endforeach>
				@else
					<fieldset class="form-group">
						<label for="{{ $input['props']['id'] }}">{!! $input['label'] !!}</label>
						<div class="{{$input['parent_class']}}">
							<span class="input-group-addon">{!! $input['input-group-addon'] !!}</span>
							<input @foreach($input['props'] as $prop => $value) {{$prop}}="{{$value}}"@endforeach>
						</div>
					</fieldset>
				@endif
			@endforeach

			<button type="submit" class="btn btn-primary">{{$data['form_params']['button_text']}}</button>

			@if(isset($data['form_params']['extra_button']))
				{!!$data['form_params']['extra_button']!!}
			@endif

		</form>
	</div>
</div>
@endsection