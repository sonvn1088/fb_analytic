@extends('adminlte::page')

@section('title', 'Manage FBAC')

@section('content_header')
    <h1>Account #{{$account->id}}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.accounts.update', $account->id]]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="{{ $account->first_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="{{ $account->last_name }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="text" name="password" class="form-control" value="{{ $account->password }}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="ol_password">Old Password</label>
                                <input type="text" name="ol_password" class="form-control" value="{{ $account->old_password }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" name="email" class="form-control" value="{{ $account->email .'|'. $account->password_email}}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="text" name="birthday" class="form-control" value="{{ $account->birthday }}" readonly>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fb_id">FB ID</label>
                                <a href="https://www.facebook.com/{{$account->fb_id}}" target="_blank"
                                   class="btn btn-md btn-primary btn-block">
                                    {{$account->fb_id}}</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="friends">Profile</label>
                                <a href="javascript:$.ajax('{{route('admin.accounts.profile', $account->profile)}}')"
                                   target="_blank" class="btn btn-md btn-info btn-block">
                                    {{$account->profile}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="friends">Friends</label>
                                <input type="text" name="friends" class="form-control" value="{{ $account->friends }}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="groups">Groups</label>
                                <input type="text" name="groups" class="form-control" value="{{ $account->groups }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="created_at">Created</label>
                                <input type="text" name="created_at" class="form-control" value="{{ $account->created_at }}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="updated_at">Updated</label>
                                <input type="text" name="updated_at" class="form-control" value="{{ $account->updated_at }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                {!! Form::select('status', $account->statuses, $account->status['value'], ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="on_server">On server</label>
                                {!! Form::select('on_server', $account->yesNo, $account->on_server['value'], ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="status">Backup</label>
                                <a href="{{route('admin.accounts.view_friends', $account->id)}}" target="_blank"
                                   class="btn btn-md btn-success btn-block">
                                    {{$account->backup}}</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="token">Token</label>
                                <input type="text" name="token" class="form-control" value="{{ $account->token }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="status">Group</label>
                                {!! Form::select('group_id', $groups, $account->group_id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="role">Role</label>
                                {!! Form::select('role', ['' => '--']+$account->roles, $account->role['value'], ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary btn-flat ">Save</button>
                    <a class="btn btn-info btn-flat" href="{{route('admin.accounts.change_password', $account->id)}}">Change Password</a>
                    <a class="btn btn-info btn-flat" href="{{route('admin.accounts.generate_token', $account->id)}}">Generate Token</a>
                    <a class="btn btn-info btn-flat" href="{{route('admin.accounts.update_info', $account->id)}}">Update Info</a>
                    <a class="btn btn-info btn-flat" href="{{route('admin.accounts.backup_friends', $account->id)}}">Backup Friends</a>
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