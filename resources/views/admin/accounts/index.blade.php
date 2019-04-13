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
                                <th>Name</th>
                                <th>Profile</th>
                                <th>Friends</th>
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
                    {
                        data: 'first_name', name: 'name', orderable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="https://www.facebook.com/'+row.fb_id+'" target="_bank" title="View on Facebook">'+data + ' ' + row.middle_name + ' ' + row.last_name+'</a>';
                        }
                    },
                    {
                        data: 'profile',
                        name: 'profile',
                        render: function ( data, type, row, meta ) {
                            return '<a href="javascript:$.ajax(\'{{ route('admin.accounts.profile') }}/'+data+'\')"  title="Open profile" class="btn btn-xs btn-success btn-block"><i class="glyphicon glyphicon-eye-open"></i> '+data+'</a>';
                        }
                    },
                    {data: 'friends', name: 'friends'},

                    {
                        data: 'group_id', name: 'group_id',
                    },
                    {
                        data: 'role', name: 'role',
                        render: function ( data, type, row, meta ) {
                            return data.label;
                        }
                    },
                    {
                        data: 'status', name:'status',
                        render: function ( data, type, row, meta ) {
                            return data.label;
                        }
                    },
                    {
                        data: 'on_server', name:'on_server',
                        render: function ( data, type, row, meta ) {
                            return data.label;
                        }},
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