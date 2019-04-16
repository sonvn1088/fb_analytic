@extends('adminlte::page')

@section('title', 'Manage browsers')

@section('content_header')
    <h1>Browsers - <a href="{{route('admin.browsers.import')}}">Import</a></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="browsers-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
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
            $('#browsers-table').DataTable({
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.browsers.list') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'created_at', name: 'created_at'},
                ]
            });

        });
    </script>
@stop