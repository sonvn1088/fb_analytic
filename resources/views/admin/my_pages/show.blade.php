@extends('adminlte::page')

@section('title', 'Manage FBAC')

@section('content_header')
    <h1>@if ($myPage->id) {{'Page #'.$myPage->name}} @else New page @endif</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.my_pages.save', $myPage->id?:0]]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fb_id">FB ID</label>
                                <input type="text" name="fb_id" class="form-control" value="{{ $myPage->fb_id }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $myPage->name }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="like">Like</label>
                                <input type="text" name="like" class="form-control" value="{{ $myPage->like }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="follow">Follow</label>
                                <input type="text" name="follow" class="form-control" value="{{ $myPage->follow }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                {!! Form::select('status', $myPage->statuses, $myPage->status['value'], ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="country">Country</label>
                                {!! Form::select('country', ['vn' => 'Vietnam', 'th' => 'Thailand', 'tw' => 'Taiwan', 'id' => 'Indonesia', 'ot' => 'Other'], $myPage->country, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="created_at">Created</label>
                                <input type="text" name="created_at" class="form-control" value="{{ $myPage->created_at }}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="updated_at">Updated</label>
                                <input type="text" name="updated_at" class="form-control" value="{{ $myPage->updated_at }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="group_id">Group</label>
                                {!! Form::select('group_id', $groups, $myPage->group_id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="token">Token</label>
                                <input type="text" name="token" class="form-control" value="{{ $myPage->token }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="site_ids">Sites</label>
                                {!! Form::select('site_ids[]', $sites, $myPage->site_ids, ['multiple'=>'multiple', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-flat ">Save</button>
                    @if($myPage->id)
                    <a class="btn btn-info btn-flat" href="{{route('admin.my_pages.update_info', $myPage->id)}}">Update Info</a>
                    @endif
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