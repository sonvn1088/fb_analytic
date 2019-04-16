@extends('adminlte::page')

@section('title', 'App # '.$app->name)

@section('content_header')
    <h1>@if ($app->id) {{'Page #'. $app->name}} @else New app @endif</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.apps.save', $app->id?:0]]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $app->name }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="key">Key</label>
                                <input type="text" name="key" class="form-control" value="{{ $app->key }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="secret">Secret</label>
                                <input type="text" name="secret" class="form-control" value="{{ $app->secret }}">
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

@stop