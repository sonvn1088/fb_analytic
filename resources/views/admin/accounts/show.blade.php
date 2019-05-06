@extends('adminlte::page')

@section('title', 'Account #'.$account->first_name.' '.$account->middle_name.' '.$account->last_name)

@section('content_header')
    <h1>Account #{{$account->first_name}} {{$account->middle_name}} {{$account->last_name}}</h1>
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
                                <label for="first_name">First name</label>
                                <input type="text" name="first_name" class="form-control" value="{{ $account->first_name }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="last_name">Last name</label>
                                <input type="text" name="last_name" class="form-control" value="{{ $account->last_name }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input type="text" name="password" class="form-control" value="{{ $account->password }}|{{ $account->old_password }}" readonly>
                                    <span class="input-group-btn">
                                        <a class="btn btn-info" href="{{route('admin.accounts.change_password', $account->id)}}" type="button">Change</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="scanned_at">Scan</label>
                                <div class="input-group">
                                    <input type="text" name="scanned_at" class="form-control" value="{{ $account->scanned_at }}" readonly>
                                    <span class="input-group-btn">
                                        <a class="btn btn-info" href="{{route('admin.accounts.scan_accounts', $account->id)}}" type="button">Scan</a>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="token">Full Token</label>
                                <div class="input-group">
                                    <input type="text" name="token" class="form-control" value="{{ $account->token }}">
                                    <span class="input-group-btn">
                                        <a class="btn btn-info" href="{{route('admin.accounts.generate_token', $account->id)}}" type="button">Generate</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="backup">Backup</label>
                                <div class="input-group">
                                    <a href="{{route('admin.accounts.view_friends', $account->id)}}" target="_blank"
                                       class="btn btn-md btn-success btn-block">
                                        {{$account->backup?:'Not yet'}}</a>
                                    <span class="input-group-btn">
                                        <a class="btn btn-info" href="{{route('admin.accounts.backup_friends', $account->id)}}" type="button">Backup</a>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="app_token">App Token</label>
                                <div class="input-group">
                                    <input type="text" name="app_token" class="form-control" value="{{ $account->app_token }}">
                                    <span class="input-group-btn">
                                        <a class="btn btn-info" href="javascript:$.ajax('{{route('admin.accounts.token_app', $account->id)}}')" target="_blank" type="button">Generate</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="backup">App</label>
                                {!! Form::select('app_id', $apps, $account->app_id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="friends">Profile</label>
                                <div class="input-group">
                                    <input type="number" name="profile" class="form-control" value="{{$account->profile}}">
                                    @if($account->profile)
                                    <span class="input-group-btn">
                                        <a href="javascript:$.ajax('{{route('admin.accounts.profile', $account->profile)}}')"
                                           target="_blank" class="btn btn-md btn-info btn-block">
                                            Profile {{$account->profile}}</a>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fb_id">FB ID</label>
                                <a href="https://www.facebook.com/{{$account->fb_id}}" target="_blank"
                                   class="btn btn-md btn-primary btn-block">
                                    {{$account->fb_id}}</a>
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
                                <label for="email">Email</label>
                                <div class="input-group">
                                    <input type="text" name="email" class="form-control" value="{{ $account->email .'|'. $account->email_password}}" readonly>
                                    <span class="input-group-btn">
                                        <a class="btn btn-info" href="{{route('admin.accounts.change_email_password', $account->id)}}" type="button">Generate Password</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="text" name="birthday" class="form-control" value="{{ $account->birthday }}">
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="friends">Friends</label>
                                <input type="text" name="friends" class="form-control" value="{{ $account->friends }}">
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
                                <label for="group">Group</label>
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
                    <a class="btn btn-info btn-flat" href="{{route('admin.accounts.update_info', $account->id)}}">Update Info</a>
                    <a class="btn btn-danger btn-flat" href="{{route('admin.accounts.delete', $account->id)}}">Delete</a>
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