@extends('adminlte::page')

@section('title', 'Manage FBAC')

@section('content_header')
    <h1>@if ($site->id) {{'Page #'.$site->name}} @else New site @endif</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.sites.save', $site->id?:0]]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $site->name }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                {!! Form::select('status', $site->statuses, $site->status['value'], ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="domain">Domain</label>
                                <input type="text" name="domain" class="form-control" value="{{ $site->domain }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="path">Path</label>
                                <input type="text" name="path" class="form-control" value="{{ $site->path }}">
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