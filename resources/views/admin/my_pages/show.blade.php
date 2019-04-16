@extends('adminlte::page')

@section('title', 'Page # '.$myPage->name)

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
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ $myPage->username }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="like">Like</label>
                                <input type="text" name="like" class="form-control" value="{{ $myPage->like?:0 }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="follow">Follow</label>
                                <input type="text" name="follow" class="form-control" value="{{ $myPage->follow?:0 }}">
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
                                <label for="blocked_at">Blocked</label>
                                <div class="input-group">
                                    <input type="text" name="blocked_at" class="form-control datetimepicker" value="{{ $myPage->blocked_at }}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>

                            </div>
                        </div>

                    </div>

                    @if($myPage->id)
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
                    @endif
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="country">Country</label>
                                {!! Form::select('country', ['vn' => 'Vietnam', 'th' => 'Thailand', 'tw' => 'Taiwan', 'id' => 'Indonesia', 'ot' => 'Other'], $myPage->country, ['class' => 'form-control']) !!}
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
            $('.datetimepicker').datetimepicker({
                format:'HH:mm DD/MM/YYYY ',
            });
        });
    </script>
@stop