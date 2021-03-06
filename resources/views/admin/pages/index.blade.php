@extends('adminlte::page')

@section('title', 'Manage pages')

@section('content_header')
    <a class="btn btn-info btn-flat pull-right" href="{{route('admin.pages.create')}}">Add Page</a>
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
                                <th>Like</th>
                                <th>Follow</th>
                                <th>Type</th>
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
            $('#pages-table').DataTable({
                serverSide: true,
                responsive: true,
                processing: true,
                order: [[0, 'desc']],
                ajax: "{{ route('admin.pages.list') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'name', name: 'name',
                        render: function ( data, type, row, meta ) {
                            return '<a href="https://www.facebook.com/'+(row.username?row.username:row.fb_id)+
                                '" target="_bank" title="View">' + data+'</a>';
                        }
                    },
                    {
                        data: 'username', name: 'username',
                    },
                    {
                        data: 'like', name: 'like', className: "text-center",
                        render: $.fn.dataTable.render.number(',')
                    },
                    {
                        data: 'follow', name: 'follow', className: "text-center",
                        render: $.fn.dataTable.render.number(',')
                    },
                    {
                        data: 'type', name: 'type',
                        render: function ( data, type, row, meta ) {
                            return data == 1?'VN':'TH';
                        }
                    },
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="{{ route('admin.pages') }}/'+row.id+ '" class="btn btn-xs btn-primary" target="_blank">' +
                                    '<i class="glyphicon glyphicon-edit"></i> Edit</a>';
                        }
                    },
                ]
            });

        });
    </script>
@stop