@extends('adminlte::page')

@section('title', 'Manage FBAC')

@section('content_header')
    <a class="btn btn-info btn-flat pull-right" href="{{route('admin.my_pages.create')}}">Add Page</a>
    <h1>Pages</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="pages-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>FB ID</th>
                                <th>Like</th>
                                <th>Follow</th>
                                <th>Group</th>
                                <th>Editor</th>
                                <th>In Scheduled</th>
                                <th>Status</th>
                                <th>Edit</th>
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
            $('#pages-table').DataTable({
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.my_pages.list') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name', orderable: false},
                    {data: 'username', name: 'username', orderable: false},
                    {
                        data: 'fb_id',
                        name: 'fb_id',
                        orderable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="https://www.facebook.com/'+data+'" target="_bank" title="View on Facebook" ' +
                                    'class="btn btn-xs btn-info btn-block">' +
                                    '<i class="glyphicon glyphicon-eye-open"></i> '+data+'</a>';
                        }
                    },
                    {
                        data: 'like', name: 'like',
                        render: $.fn.dataTable.render.number(',')
                    },
                    {
                        data: 'follow', name: 'follow',
                        render: $.fn.dataTable.render.number(',')
                    },

                    {
                        data: 'group_id', name: 'group_id',

                    },
                    {
                        data: 'editor', name: 'editor',
                        render: function ( data, type, row, meta ) {
                            return '<a href="{{route('admin.accounts.show')}}/'+data.id+'" target="_blank">'
                                    +data.first_name + ' ' + data.last_name + '<a/> ('
                                    + '<a title="Open profile" href="javascript:$.ajax(\''+ '{{route('admin.accounts.profile')}}/'+data.profile+'\')">Open profile<a/>)'
                                    ;
                        }

                    },
                    {
                        data: 'scheduled_posts', name: 'scheduled_posts',
                    },
                    {data: 'status', name: 'status'},
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="{{ route('admin.my_pages') }}/'+row.id+ '" class="btn btn-xs btn-primary">' +
                                    '<i class="glyphicon glyphicon-edit"></i> Edit</a>';
                        }
                    },
                ]
            });

        });
    </script>
@stop