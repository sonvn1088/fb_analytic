@extends('adminlte::page')

@section('title', 'Manage FBAC')

@section('content_header')
    <h1>@if ($group->id) {{'Page #'.$group->name}} @else New group @endif</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.groups.save', $group->id?:0]]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $group->name }}">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-flat ">Save</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')

@stop
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
        });
    </script>
@stop