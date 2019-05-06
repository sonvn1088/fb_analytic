@extends('adminlte::page')

@section('title', 'Manage articles')

@section('content_header')
    <h1>Articles</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="links-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Link</th>
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
                order: [[1, 'desc']],
                processing: true,
                pageLength: 160,
                ajax: "{{ route('admin.articles.list') }}",
                dom: 'lifrtp',
                columns: [
                    {data: 'name', name: 'name'},
                    {
                        data: 'link', name: 'link',
                        render: function ( data, type, row, meta ) {
                            return '<a href="'+data+'" target="_bank" title="View">' + data+'</a>';
                        }
                    },
                    {data: 'created_time', name: 'created_time'},
                ]
            });

        });
    </script>
@stop