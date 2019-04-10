@extends('adminlte::page')

@section('title', 'Manage FBAC')

@section('content_header')
    <a class="btn btn-info btn-flat pull-right" href="{{route('admin.sites.create')}}">Add Site</a>
    <h1>Sites</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="sites-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Domain</th>
                                <th>Path</th>
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
            $('#sites-table').DataTable({
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.sites.list') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name', orderable: false},
                    {
                        data: 'domain', name: 'domain', orderable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="'+data+'" target="_blank">'+data+'</a>';
                        }
                    },
                    {data: 'path', name: 'path', orderable: false},
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="{{ route('admin.sites') }}/'+row.id+ '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                        }
                    },
                ]
            });

        });
    </script>
@stop