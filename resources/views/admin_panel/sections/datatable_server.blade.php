@extends('admin_panel/dashboard')

@section('content')
<?php
$count = 1;
?>
@if(isset($data['datepicker']))
    <div class="row">
        <div class="interval_inputs col-xs-12" data-comlumnname="{{$data['datepicker']['comlumnNameSQL']}}" data-columnfilter="{{$data['datepicker']['columnIndex']}}">
            <div>
                <label for="minDate">
                    {{$data['datepicker']['min_label']}}: 
                    <input class="minDate" id="minDate" type="text">&nbsp;<i class="min fa fa-calendar"></i>
                </label>
            </div>
            <div>
                <label for="maxDate">
                    {{$data['datepicker']['max_label']}}:
                    <input id="maxDate" class="maxDate" name="max" type="text">&nbsp;<i class="max fa fa-calendar"></i>
                </label>
            </div>
        </div>
    </div>
@endif
@foreach($data['tables'] as $table)
    <div class="row">
        <div id="wrapper_{{$count}}" class=" col-xs-12">
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
            <div class="box {{$table['box_class']}}">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$table['title']}}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <table id="tableData_{{$count}}" class="myDataTable table table-striped table-bordered dt-responsive nowrap dataTable no-footer dtr-inline collapsed" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                @foreach($table['columns'] as $key => $column)
                                    <th data-name="{{$key}}">{!!$column!!}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                @foreach($table['columns'] as $key => $column)
                                    <th data-name="{{$key}}">{!!$column!!}</th>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $count += 1; ?>
@endforeach
@endsection