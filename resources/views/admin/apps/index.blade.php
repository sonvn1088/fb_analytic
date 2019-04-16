@extends('adminlte::page')

@section('title', 'Manage Apps')

@section('content_header')
    <a class="btn btn-info btn-flat pull-right" href="{{route('admin.apps.create')}}">Add App</a>
    <h1>Apps</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="apps-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Key</th>
                                <th>Secret</th>
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
            $('#apps-table').DataTable({
                serverSide: true,
                responsive: true,
                processing: true,
                order: [[0, 'desc']],
                ajax: "{{ route('admin.apps.list') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'name', name: 'name',
                    },
                    {
                        data: 'key', name: 'key',
                    },
                    {
                        data: 'secret', name: 'secret'
                    },
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="{{ route('admin.apps.show') }}/'+row.id+ '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                        }
                    },
                ]
            });

        });
    </script>
@stop