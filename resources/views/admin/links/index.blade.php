@extends('adminlte::page')

@section('title', 'Manage links')

@section('content_header')
    <h1>Links</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="links-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Url</th>
                                <th>Created At</th>
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
            $('#links-table').DataTable({
                serverSide: true,
                responsive: true,
                order: [[0, 'desc']],
                ajax: "{{ route('admin.links.list') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {data: 'title', name: 'title'},
                    {
                        data: 'url', name: 'url',
                        render: function ( data, type, row, meta ) {
                            return '<a href="'+data+'" target="_bank" title="View">' + decodeURI(data)+'</a>';
                        }
                    },
                    {data: 'created_at', name: 'created_at'},
                ]
            });

        });
    </script>
@stop