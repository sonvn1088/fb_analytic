@extends('adminlte::page')

@section('title', 'New Account')

@section('content_header')
    <h1>New Account</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.accounts.store']]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="first_name">First name</label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="last_name">Last name</label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="text" name="password" class="form-control" value="{{ old('password') }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="email_password">Email Password</label>
                                <input type="text" name="email_password" class="form-control" value="{{ old('email_password') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="profile">Profile</label>
                                <input type="number" name="profile" class="form-control" value="{{ old('profile') }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fb_id">FB ID</label>
                                <input type="number" name="fb_id" class="form-control" value="{{ old('fb_id') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <input type="text" name="birthday" class="form-control" value="{{ old('birthday') }}">
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