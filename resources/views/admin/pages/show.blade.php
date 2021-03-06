@extends('adminlte::page')

@section('title', 'Page # '.$page->name)

@section('content_header')
    <a class="btn btn-info btn-flat pull-right" href="{{route('admin.pages.create')}}">Add Page</a>
    <h1>@if ($page->id) {{'Page #'.$page->name}} @else New page @endif</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.pages.save', $page->id?:0]]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fb_id">FB ID</label>
                                <input type="text" name="fb_id" class="form-control" value="{{ $page->fb_id }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ $page->username }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="like">Like</label>
                                <input type="text" name="like" class="form-control" value="{{ $page->like?:0 }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="follow">Follow</label>
                                <input type="text" name="follow" class="form-control" value="{{ $page->follow?:0 }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $page->name?:'' }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Type</label>
                                {!! Form::select('type', [1 => 'VN', 2 => 'TH'], $page->type, ['class' => 'form-control']) !!}
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