@extends('adminlte::page')

@section('title', 'Manage FBAC')

@section('content_header')
    <h1>Accounts</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="accounts-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Profile</th>
                                <th>Friends</th>
                                <th>FB ID</th>
                                <th>Group</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Server</th>
                                <th>Backup</th>
                                <th>View</th>
                            </tr>
                        </thead>
                    </table>
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
            $('#accounts-table').DataTable({
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.accounts.list') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'first_name', name: 'first_name', orderable: false},
                    {data: 'last_name', name: 'last_name', orderable: false},
                    {
                        data: 'profile',
                        name: 'profile',
                        render: function ( data, type, row, meta ) {
                            return '<a href="javascript:$.ajax(\'{{ route('admin.accounts.profile') }}/'+data+'\')"  title="Open profile" class="btn btn-xs btn-success btn-block"><i class="glyphicon glyphicon-eye-open"></i> '+data+'</a>';
                        }
                    },
                    {data: 'friends', name: 'friends'},
                    {
                        data: 'fb_id',
                        name: 'fb_id',
                        orderable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="https://www.facebook.com/'+data+'" target="_bank" title="View on Facebook" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-eye-open"></i> '+data+'</a>';
                        }
                    },
                    {
                        data: 'group_id', name: 'group_id',
                    },
                    {
                        data: 'role', name: 'role',
                    },
                    {data: 'status', name:'status'},
                    {data: 'on_server', name:'on_server'},
                    {data: 'backup', name:'backup'},
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="{{ route('admin.accounts.show') }}/'+row.id+ '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-eye-open"></i> View</a>';
                        }
                    },

                ]
            });

        });
    </script>
@stop