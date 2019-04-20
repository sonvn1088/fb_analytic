@extends('adminlte::page')

@section('title', 'Manage Accounts')

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
                                <th>FirstName</th>
                                <th>LastName</th>
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
                order: [[ 7, 'desc']],
                serverSide: true,
                responsive: true,
                processing: true,
                ajax: "{{ route('admin.accounts.list') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {
                        data: 'first_name', name: 'first_name', orderable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="https://www.facebook.com/'+row.fb_id+'" target="_bank" title="View on Facebook">'+data +'</a>';
                        }
                    },
                    {
                        data: 'last_name', name: 'last_name', orderable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="https://www.facebook.com/'+row.fb_id+'" target="_bank" title="View on Facebook">'+data+'</a>';
                        }
                    },
                    {
                        data: 'profile', name: 'profile', searchable : false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="javascript:$.ajax(\'{{ route('admin.accounts.profile') }}/'+data+'\')"  title="Open profile" class="btn btn-xs btn-success btn-block"><i class="glyphicon glyphicon-eye-open"></i> '+data+'</a>';
                        }
                    },
                    {data: 'friends', name: 'friends',  searchable : false},
                    {
                        data: 'group_id', name: 'group_id',  searchable : false
                    },
                    {
                        data: 'role', name: 'role',  searchable : false,
                        render: function ( data, type, row, meta ) {
                            return data.label;
                        }
                    },
                    {
                        data: 'status', name:'status',  searchable : false,
                        render: function ( data, type, row, meta ) {
                            return '<span class="text-'+(data.value=={{\App\Models\Account::ACTIVE}}?'success':(data.value=={{\App\Models\Account::DISABlED}}?'muted':'danger'))+'">'
                                    +data.label+'</span>'
                        }
                    },
                    {
                        data: 'on_server', name:'on_server',  searchable : false,
                        render: function ( data, type, row, meta ) {
                            return data.label;
                        }},
                    {data: 'backup', name:'backup',  searchable : false},
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